<?php

namespace App\Jobs;

use App\Models\MotorcycleImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class GenerateThumbnail implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(public readonly MotorcycleImage $image) {}

    public function handle(): void
    {
        $disk  = MotorcycleImage::disk();
        $thumb = preg_replace('/\.webp$/', '_thumb.webp', $this->image->path);

        if (Storage::disk($disk)->exists($thumb)) {
            return;
        }

        try {
            $content = Storage::disk($disk)->get($this->image->path);

            $webp = (new ImageManager(new GdDriver()))
                ->decodeBinary($content)
                ->scaleDown(width: 600)
                ->encode(new WebpEncoder(80));

            Storage::disk($disk)->put($thumb, (string) $webp, [
                'visibility'   => 'public',
                'ContentType'  => 'image/webp',
                'CacheControl' => 'max-age=31536000, immutable',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}");
        }
    }
}
