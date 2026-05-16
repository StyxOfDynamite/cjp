<?php

namespace App\Jobs;

use App\Models\MotorcycleImage;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class GenerateThumbnail implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(public readonly MotorcycleImage $image) {}

    public function uniqueId(): string
    {
        return (string) $this->image->id;
    }

    public function handle(): void
    {
        if ($this->image->has_thumbnail) {
            return;
        }

        $disk  = MotorcycleImage::disk();
        $thumb = preg_replace('/\.webp$/', '_thumb.webp', $this->image->path);

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

            $this->image->update(['has_thumbnail' => true]);
        } catch (\Throwable $e) {
            Log::warning("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}");
        }
    }
}
