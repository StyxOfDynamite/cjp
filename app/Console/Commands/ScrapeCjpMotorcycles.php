<?php

namespace App\Console\Commands;

use App\Models\Motorcycle;
use App\Models\MotorcycleImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeCjpMotorcycles extends Command
{
    protected $signature = 'scrape:cjp
                            {--skip-details : Only use URL slug data — skip fetching individual product pages}
                            {--skip-images : Do not download images}
                            {--dry-run : Preview what would be imported without saving anything}
                            {--limit= : Only process this many products (useful for testing)}';

    protected $description = 'Scrape all CJP Motorcycles listings from the Weebly sitemap and import into the database';

    const BASE_URL = 'https://cjpmotorcycles.weebly.com';
    const SITEMAP_URL = 'https://cjpmotorcycles.weebly.com/sitemap.xml';
    const MULTI_WORD_MAKES = ['Harley Davidson'];

    public function handle(): int
    {
        $this->info('Fetching sitemap...');

        $response = Http::timeout(30)->get(self::SITEMAP_URL);

        if (! $response->ok()) {
            $this->error('Failed to fetch sitemap: HTTP ' . $response->status());
            return self::FAILURE;
        }

        $urls = $this->parseSitemap($response->body());

        if (empty($urls)) {
            $this->error('No product URLs found in sitemap.');
            return self::FAILURE;
        }

        $this->info('Found ' . count($urls) . ' products in sitemap.');

        if ($limit = $this->option('limit')) {
            $urls = array_slice($urls, 0, (int) $limit);
            $this->info("Limiting to {$limit} products.");
        }

        // Parse basic data from each URL slug upfront
        $products = array_map(fn ($url) => $this->parseSlug($url), $urls);

        if ($this->option('dry-run')) {
            $this->table(
                ['Make', 'Model', 'Year', 'Price (£)', 'In Stock', 'Mileage (mi)', 'Engine (cc)'],
                array_map(fn ($p) => [
                    $p['make'],
                    $p['model'],
                    $p['year'] ?? '—',
                    $p['price'] ? number_format($p['price'], 2) : '—',
                    $p['in_stock'] ? 'Yes' : 'No',
                    $p['mileage_miles'] ?? '—',
                    $p['engine_size_cc'] ?? '—',
                ], $products)
            );
            $inStock = count(array_filter($products, fn ($p) => $p['in_stock']));
            $this->info("{$inStock} in stock, " . (count($products) - $inStock) . ' sold.');
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        foreach ($products as $product) {
            if (! $this->option('skip-details')) {
                usleep(500_000); // 0.5s between requests
                $detail = $this->fetchDetail($product['detail_url']);
                // Only override slug-parsed values if the detail page found something
                $product = array_merge($product, array_filter($detail, fn ($v) => $v !== null && $v !== []));
            }

            $this->importMotorcycle($product);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $imported = Motorcycle::count();
        $this->info("Import complete. {$imported} total motorcycles in database.");

        return self::SUCCESS;
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
            'year'          => $year,
            'mileage_miles' => $mileage,
            'price'         => $price,
            'color'         => $color,
            'in_stock'      => $inStock,
            'detail_url'    => $url,
            'images'        => [],
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

        if (! $make) {
            [$make, $model] = array_pad(explode(' ', $name, 2), 2, '');
        }

        return [
            'make'           => $make,
            'model'          => $model,
            'engine_size_cc' => $engineSize,
        ];
    }

    private function extractColor(string $slug): ?string
    {
        $colors = ['black', 'red', 'blue', 'white', 'silver', 'grey', 'gray', 'green',
                   'yellow', 'orange', 'purple', 'brown', 'gold', 'bronze', 'maroon'];

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
        $data = ['description' => null, 'year' => null, 'mileage_miles' => null,
                 'engine_size_cc' => null, 'images' => []];

        foreach (['product-description', 'wsite-product-description', 'wsite-description'] as $cls) {
            $nodes = $xpath->query("//*[contains(@class, '{$cls}')]");
            if ($nodes->length > 0) {
                $data['description'] = trim($nodes->item(0)->textContent);
                break;
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
            if (! $src) continue;
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
            'make'  => $data['make'],
            'model' => $data['model'],
            'year'  => $data['year'],
            'price' => $data['price'] ?? 0,
        ]);

        $motorcycle->fill(array_filter([
            'stock_quantity' => $data['in_stock'] ? 1 : 0,
            'description'    => $data['description'] ?? null,
            'mileage_miles'  => $data['mileage_miles'] ?? null,
            'engine_size_cc' => $data['engine_size_cc'] ?? null,
            'color'          => $data['color'] ?? null,
        ], fn ($v) => $v !== null));

        if (! $motorcycle->price) {
            $motorcycle->price = 0;
        }

        $motorcycle->save();

        if ($this->option('skip-images') || $motorcycle->images()->count() > 0) {
            return;
        }

        $urls = $data['images'] ?? [];
        if (! empty($urls)) {
            $this->downloadImages($motorcycle, $urls);
        }
    }

    private function downloadImages(Motorcycle $motorcycle, array $urls): void
    {
        foreach (array_slice($urls, 0, 10) as $i => $url) {
            try {
                $response = Http::timeout(30)->get($url);
                if (! $response->ok()) continue;

                $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $path = 'motorcycles/' . $motorcycle->id . '/' . Str::uuid() . '.' . $ext;

                Storage::disk('public')->put($path, $response->body());

                MotorcycleImage::create([
                    'motorcycle_id' => $motorcycle->id,
                    'path'          => $path,
                    'alt_text'      => "{$motorcycle->year} {$motorcycle->make} {$motorcycle->model}",
                    'sort_order'    => $i,
                    'is_primary'    => $i === 0,
                ]);
            } catch (\Exception) {
                // Skip images that fail
            }
        }
    }
}
