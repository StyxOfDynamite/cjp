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

        $disk    = MotorcycleImage::disk();
        $source  = preg_replace('/\.webp$/', '.source', $this->image->path);
        $thumb   = preg_replace('/\.webp$/', '_thumb.webp', $this->image->path);
        $options = ['visibility' => 'public', 'ContentType' => 'image/webp', 'CacheControl' => 'max-age=31536000, immutable'];
        $storage = Storage::disk($disk);
        $manager = new ImageManager(new GdDriver());

        try {
            // Pass 1: convert source file to full WebP, capped at 2000px.
            // decodeStream() feeds raw bytes directly to GD without buffering
            // them as a PHP string, keeping the heap footprint low.
            if ($storage->exists($source)) {
                $stream = $storage->readStream($source);
                $img    = $manager->decodeStream($stream);
                fclose($stream);

                $img->scaleDown(width: 2000);
                $storage->put($this->image->path, (string) $img->encode(new WebpEncoder(85)), $options);

                // Free the decoded bitmap before starting the thumbnail pass.
                unset($img);
                gc_collect_cycles();

                $storage->delete($source);
            }

            // Pass 2: thumbnail from the stored full WebP (now max 2000px).
            // Memory from pass 1 is fully released before this decode begins.
            $stream    = $storage->readStream($this->image->path);
            $thumbnail = $manager->decodeStream($stream)->scaleDown(width: 600)->encode(new WebpEncoder(80));
            fclose($stream);

            $storage->put($thumb, (string) $thumbnail, $options);
            unset($thumbnail);

            $this->image->update(['has_thumbnail' => true]);
        } catch (\Throwable $e) {
            Log::warning("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}");
        }
    }
}
