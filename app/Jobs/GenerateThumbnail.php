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
    public int $timeout = 120;

    // Imagick overflows to disk rather than growing PHP's heap when it hits
    // this cap. 64 MB is enough for thumbnails and small edits; large source
    // files spill to /tmp automatically.
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

        \Imagick::setResourceLimit(\Imagick::RESOURCETYPE_MEMORY, self::IMAGICK_MEMORY_LIMIT);
        \Imagick::setResourceLimit(\Imagick::RESOURCETYPE_MAP,    self::IMAGICK_MEMORY_LIMIT * 4);

        try {
            // Pass 1: convert source to full WebP, capped at 2000px.
            // Raw bytes are streamed to a temp file so they never live in PHP's
            // heap — Imagick reads from the path at the C level.
            if ($storage->exists($source)) {
                $tmp = $this->streamToTemp($storage, $source);

                try {
                    $imagick = new \Imagick($tmp);
                    $imagick->thumbnailImage(2000, 0);
                    $imagick->setImageFormat('webp');
                    $imagick->setImageCompressionQuality(85);
                    $storage->put($this->image->path, $imagick->getImageBlob(), $options);
                    $imagick->destroy();
                } finally {
                    @unlink($tmp);
                }

                unset($imagick);
                gc_collect_cycles();
                $storage->delete($source);
            }

            // Pass 2: thumbnail from the stored full WebP (now max 2000px).
            // Heap from pass 1 is freed before this decode starts.
            $tmp = $this->streamToTemp($storage, $this->image->path);

            try {
                $imagick = new \Imagick($tmp);
                $imagick->thumbnailImage(600, 0);
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality(80);
                $storage->put($thumb, $imagick->getImageBlob(), $options);
                $imagick->destroy();
            } finally {
                @unlink($tmp);
            }

            unset($imagick);

            $this->image->update(['has_thumbnail' => true]);
        } catch (\Throwable $e) {
            Log::warning("Thumbnail generation failed for image {$this->image->id}: {$e->getMessage()}", [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    // Copies a storage file into a local temp file via a stream, so the full
    // contents are never materialised as a PHP string on the heap.
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
