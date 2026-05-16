<?php

namespace App\Console\Commands;

use App\Models\Motorcycle;
use App\Models\MotorcycleImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ScrapeCjpMotorcycles extends Command
{
    protected $signature = 'scrape:cjp
                            {--skip-details : Only use URL slug data — skip fetching individual product pages}
                            {--skip-images : Do not download images}
                            {--dry-run : Preview what would be imported without saving anything}
                            {--limit= : Only process this many products (useful for testing)}
                            {--fresh : Clear scrape log and re-process all URLs from scratch}
                            {--skip= : Skip the first N URLs in the sitemap}';

    protected $description = 'Scrape all CJP Motorcycles listings from the Weebly sitemap and import into the database';

    const BASE_URL = 'https://cjpmotorcycles.weebly.com';
    const SITEMAP_URL = 'https://cjpmotorcycles.weebly.com/sitemap.xml';
    const MULTI_WORD_MAKES = ['Harley Davidson'];

    public function handle(): int
    {
        $this->log('Fetching sitemap...');

        $response = Http::timeout(30)->get(self::SITEMAP_URL);

        if (!$response->ok()) {
            $this->log('Failed to fetch sitemap: HTTP ' . $response->status(), 'error');
            return self::FAILURE;
        }

        $urls = $this->parseSitemap($response->body());

        if (empty($urls)) {
            $this->log('No product URLs found in sitemap.', 'error');
            return self::FAILURE;
        }

        $this->log('Found ' . count($urls) . ' products in sitemap.');

        if ($this->option('fresh')) {
            DB::table('scrape_log')->truncate();
            $this->log('Scrape log cleared.');
        }

        if ($skip = $this->option('skip')) {
            $urls = array_slice($urls, (int) $skip);
            $this->log("Skipping first {$skip} URLs.");
        }

        $done = DB::table('scrape_log')->pluck('url')->flip();
        $urls = array_values(array_filter($urls, fn($u) => !$done->has($u)));

        if (empty($urls)) {
            $this->log('All URLs already scraped. Use --fresh to re-process.');
            return self::SUCCESS;
        }

        $total = count($urls);
        $this->log("{$total} remaining to scrape.");

        if ($limit = $this->option('limit')) {
            $urls = array_slice($urls, 0, (int) $limit);
            $total = count($urls);
            $this->log("Limiting to {$total} products.");
        }

        $products = array_map(fn($url) => $this->parseSlug($url), $urls);

        if ($this->option('dry-run')) {
            $this->table(
                ['Make', 'Model', 'Year', 'Price (£)', 'In Stock', 'Mileage (mi)', 'Engine (cc)'],
                array_map(fn($p) => [
                    $p['make'],
                    $p['model'],
                    $p['year'] ?? '—',
                    $p['price'] ? number_format($p['price'], 2) : '—',
                    $p['in_stock'] ? 'Yes' : 'No',
                    $p['mileage_miles'] ?? '—',
                    $p['engine_size_cc'] ?? '—',
                ], $products)
            );
            $inStock = count(array_filter($products, fn($p) => $p['in_stock']));
            $this->log("{$inStock} in stock, " . ($total - $inStock) . ' sold.');
            return self::SUCCESS;
        }

        foreach ($products as $i => $product) {
            if (!$this->option('skip-details')) {
                $detail = $this->fetchDetail($product['detail_url']);
                $product = array_merge($product, array_filter($detail, fn($v) => $v !== null && $v !== []));
            }

            $this->importMotorcycle($product);
            DB::table('scrape_log')->insertOrIgnore(['url' => $product['detail_url']]);

            $n = $i + 1;
            if ($n % 10 === 0 || $n === $total) {
                $this->log("Progress: {$n}/{$total} scraped.");
            }
        }

        $imported = Motorcycle::count();
        $this->log("Import complete. {$imported} total motorcycles in database.");

        return self::SUCCESS;
    }

    private function log(string $message, string $level = 'info'): void
    {
        Log::$level('[scrape:cjp] ' . $message);
        $this->$level($message);
    }

    // -------------------------------------------------------------------------
    // Sitemap
    // -------------------------------------------------------------------------

    private function parseSitemap(string $xml): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($xml);
        libxml_clear_errors();

        $urls = [];
        foreach ($dom->getElementsByTagName('loc') as $loc) {
            $url = trim($loc->textContent);
            // Match product pages but skip blank placeholders like /store/p55/p.html
            if (preg_match('#/store/p\d+/#', $url) && !preg_match('#/store/p\d+/p\.html$#', $url)) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    // -------------------------------------------------------------------------
    // Slug parsing — extracts all usable data from the product URL
    // -------------------------------------------------------------------------

    private function parseSlug(string $url): array
    {
        $slug = str_replace('_', ' ', urldecode(basename($url, '.html')));
        // e.g. "HONDA CB250 NIGHTHAWK, 2001, 5787 MILES £3995"
        //      "SUZUKI GSX1400-K7 FE, 13330 MILES, 2008/08, £7995"
        //      "YAMAHA XV250-S, 2001, 6792 MILES, BLACK, £3495"

        // Year: first 4-digit year in slug
        $year = null;
        if (preg_match('/\b((?:19|20)\d{2})\b/', $slug, $m)) {
            $year = (int) $m[1];
        }

        // Product name: everything before the first comma (or before the year)
        $rawName = '';
        $firstComma = strpos($slug, ',');
        if ($firstComma !== false) {
            $rawName = trim(substr($slug, 0, $firstComma));
        } elseif ($year !== null) {
            $rawName = trim(substr($slug, 0, strpos($slug, (string) $year)));
        } else {
            $rawName = preg_replace('/\s+£[\d,]+.*$/u', '', $slug);
            $rawName = preg_replace('/\s+[\d,]+\s*miles?.*/i', '', $rawName);
        }

        // Strip descriptive prefix/suffix words that aren't part of the make/model
        $rawName = preg_replace('/^(classic|original|restored|rare|new)\s+/i', '', trim($rawName));
        $rawName = preg_replace('/\s+(sold|only|fsh|abs|lams|ulez)$/i', '', trim($rawName));

        $mileage = null;
        if (preg_match('/([\d,]+)\s*miles?/i', $slug, $m)) {
            $mileage = (int) str_replace(',', '', $m[1]);
        }

        $price = null;
        if (preg_match('/£([\d,]+)/u', $slug, $m)) {
            $price = (float) str_replace(',', '', $m[1]);
        }

        // Extract colour — comma-separated word(s) that aren't year/mileage/price/sold
        $color = $this->extractColor($slug);

        $inStock = stripos($slug, 'sold') === false;

        $parsed = $this->parseName(ucwords(strtolower($rawName)));

        return array_merge($parsed, [
            'year' => $year,
            'mileage_miles' => $mileage,
            'price' => $price,
            'color' => $color,
            'in_stock' => $inStock,
            'detail_url' => $url,
            'images' => [],
        ]);
    }

    private function parseName(string $name): array
    {
        $engineSize = null;
        if (preg_match('/\b(\d+)cc\b/i', $name, $m)) {
            $engineSize = (int) $m[1];
        }

        $make = '';
        $model = '';

        foreach (self::MULTI_WORD_MAKES as $mwMake) {
            if (stripos($name, $mwMake) === 0) {
                $make = $mwMake;
                $model = trim(substr($name, strlen($mwMake)));
                break;
            }
        }

        if (!$make) {
            [$make, $model] = array_pad(explode(' ', $name, 2), 2, '');
        }

        return [
            'make' => $make,
            'model' => $model,
            'engine_size_cc' => $engineSize,
        ];
    }

    private function extractColor(string $slug): ?string
    {
        $colors = [
            'black',
            'red',
            'blue',
            'white',
            'silver',
            'grey',
            'gray',
            'green',
            'yellow',
            'orange',
            'purple',
            'brown',
            'gold',
            'bronze',
            'maroon'
        ];

        foreach ($colors as $color) {
            if (preg_match('/,\s*' . preg_quote($color, '/') . '\s*[,£]/i', $slug)) {
                return ucfirst($color);
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Detail page
    // -------------------------------------------------------------------------

    private function fetchDetail(string $url): array
    {
        try {
            $response = Http::timeout(30)->get($url);
            return $response->ok() ? $this->parseDetail($response->body()) : [];
        } catch (\Exception) {
            return [];
        }
    }

    private function parseDetail(string $html): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $data = [
            'description' => null,
            'year' => null,
            'mileage_miles' => null,
            'engine_size_cc' => null,
            'images' => []
        ];

        // Try itemprop="description" first (Weebly structured data markup)
        $nodes = $xpath->query('//*[@itemprop="description"]');
        if ($nodes->length > 0) {
            $data['description'] = trim($nodes->item(0)->textContent);
        }

        // Fallback to common class names
        if (empty($data['description'])) {
            foreach (['product-description', 'wsite-product-description', 'wsite-description'] as $cls) {
                $nodes = $xpath->query("//*[contains(@class, '{$cls}')]");
                if ($nodes->length > 0) {
                    $data['description'] = trim($nodes->item(0)->textContent);
                    break;
                }
            }
        }

        $desc = $data['description'] ?? '';

        if (preg_match('/\b((?:19|20)\d{2})\b/', $desc, $m)) {
            $data['year'] = (int) $m[1];
        }

        if (preg_match('/([\d,]+)\s*miles?/i', $desc, $m)) {
            $data['mileage_miles'] = (int) str_replace(',', '', $m[1]);
        }

        if (preg_match('/\b(\d+)cc\b/i', $desc, $m)) {
            $data['engine_size_cc'] = (int) $m[1];
        }

        $images = [];
        foreach ($xpath->query('//img[contains(@src, "/uploads/")]') as $img) {
            $src = preg_replace('/\?.*$/', '', $img->getAttribute('src'));
            if (!$src)
                continue;
            $images[] = str_starts_with($src, 'http') ? $src : self::BASE_URL . $src;
        }
        $data['images'] = array_values(array_unique($images));

        return $data;
    }

    // -------------------------------------------------------------------------
    // Database persistence
    // -------------------------------------------------------------------------

    private function importMotorcycle(array $data): void
    {
        $motorcycle = Motorcycle::firstOrNew([
            'make' => $data['make'],
            'model' => $data['model'],
            'year' => $data['year'],
            'price' => $data['price'] ?? 0,
        ]);

        $motorcycle->fill(array_filter([
            'stock_quantity' => $data['in_stock'] ? 1 : 0,
            'description' => $data['description'] ?? null,
            'mileage_miles' => $data['mileage_miles'] ?? null,
            'engine_size_cc' => $data['engine_size_cc'] ?? null,
            'color' => $data['color'] ?? null,
        ], fn($v) => $v !== null));

        if (!$motorcycle->price) {
            $motorcycle->price = 0;
        }

        $motorcycle->save();

        if ($this->option('skip-images') || $motorcycle->images()->count() > 0) {
            return;
        }

        $urls = $data['images'] ?? [];
        if (!empty($urls)) {
            $this->downloadImages($motorcycle, $urls);
        }
    }

    private function downloadImages(Motorcycle $motorcycle, array $urls): void
    {
        $manager = new ImageManager(new GdDriver());

        foreach (array_slice($urls, 0, 10) as $i => $url) {
            try {
                $response = Http::timeout(30)->get($url);
                if (!$response->ok())
                    continue;

                $content = $response->body();
                $uuid    = Str::uuid();
                $disk    = MotorcycleImage::disk();
                $path    = 'motorcycles/' . $motorcycle->id . '/' . $uuid . '.webp';
                $thumb   = 'motorcycles/' . $motorcycle->id . '/' . $uuid . '_thumb.webp';
                $options = ['visibility' => 'public', 'ContentType' => 'image/webp', 'CacheControl' => 'max-age=31536000, immutable'];

                Storage::disk($disk)->put($path,  (string) $manager->decodeBinary($content)->encode(new WebpEncoder(85)), $options);
                Storage::disk($disk)->put($thumb, (string) $manager->decodeBinary($content)->scaleDown(width: 600)->encode(new WebpEncoder(80)), $options);

                MotorcycleImage::create([
                    'motorcycle_id' => $motorcycle->id,
                    'path'          => $path,
                    'alt_text'      => "{$motorcycle->year} {$motorcycle->make} {$motorcycle->model}",
                    'sort_order'    => $i,
                    'is_primary'    => $i === 0,
                    'has_thumbnail' => true,
                ]);
            } catch (\Exception) {
                // Skip images that fail
            }
        }
    }
}
