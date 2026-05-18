<?php

namespace App\Jobs;

use App\Models\MotorcycleImage;
use Illuminate\Contracts\Filesystem\Filesystem;
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
            // Raw bytes are streamed to a temp file rather than held in a PHP string,
            // so GD never has to share heap with the encoded source simultaneously.
            if ($storage->exists($source)) {
                $tmp = $this->streamToTemp($storage, $source);

                try {
                    $img = $manager->read($tmp);
                    $img->scaleDown(width: 2000);
                    $storage->put($this->image->path, (string) $img->encode(new WebpEncoder(85)), $options);
                } finally {
                    unset($img);
                    gc_collect_cycles();
                    @unlink($tmp);
                }

                $storage->delete($source);
            }

            // Pass 2: thumbnail from the stored full WebP (now max 2000px wide).
            // Memory is fully released from pass 1 before this decode starts.
            $tmp = $this->streamToTemp($storage, $this->image->path);

            try {
                $thumbnail = $manager->read($tmp)->scaleDown(width: 600)->encode(new WebpEncoder(80));
                $storage->put($thumb, (string) $thumbnail, $options);
            } finally {
                unset($thumbnail);
                @unlink($tmp);
            }

            $this->image->update(['has_thumbnail' => true]);
        } catch (\Throwable $e) {
            Log::warning("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}");
        }
    }

    // Streams a storage file into a local temp file in chunks — avoids loading
    // the entire file as a PHP string before GD can begin decoding.
    private function streamToTemp(Filesystem $storage, string $path): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'img_');
        $src = $storage->readStream($path);
        $dst = fopen($tmp, 'wb');
        stream_copy_to_stream($src, $dst);
        fclose($dst);
        fclose($src);

        return $tmp;
    }
}
