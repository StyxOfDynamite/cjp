<?php

namespace App\Jobs;

use App\Models\MotorcycleImage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnail implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 300;

    private const IMAGICK_MEMORY_LIMIT = 64 * 1024 * 1024;

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

        try {
            \Imagick::setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, self::IMAGICK_MEMORY_LIMIT);
            \Imagick::setResourceLimit(\Imagick::RESOURCETYPE_MAP,    self::IMAGICK_MEMORY_LIMIT * 4);

            $readPath = $storage->exists($source) ? $source : $this->image->path;
            $tmp      = $this->streamToTemp($storage, $readPath);

            try {
                $imagick = new \Imagick($tmp);

                // Scale to full-size WebP (2000px wide max), store if from source
                $imagick->thumbnailImage(2000, 0);
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality(85);

                if ($storage->exists($source)) {
                    $storage->put($this->image->path, $imagick->getImageBlob(), $options);
                    $storage->delete($source);
                }

                // Scale down further in-place for the thumbnail
                $imagick->thumbnailImage(600, 0);
                $imagick->setImageCompressionQuality(80);
                $storage->put($thumb, $imagick->getImageBlob(), $options);

                $imagick->destroy();
            } finally {
                @unlink($tmp);
            }

            $this->image->update(['has_thumbnail' => true]);
        } catch (\Throwable $e) {
            Log::error("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}", [
                'file'  => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

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
