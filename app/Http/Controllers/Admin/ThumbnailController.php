<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotorcycleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ThumbnailController extends Controller
{
    public function show(MotorcycleImage $image): RedirectResponse
    {
        $disk  = MotorcycleImage::disk();
        $thumb = preg_replace('/\.webp$/', '_thumb.webp', $image->path);

        if (Storage::disk($disk)->exists($thumb)) {
            // Already generated — permanent redirect so browser caches and skips Laravel next time
            return redirect(Storage::disk($disk)->url($thumb), 301);
        }

        // Generate thumbnail on demand
        try {
            $content = Storage::disk($disk)->get($image->path);
            $webp    = (new ImageManager(new GdDriver()))
                ->decodeBinary($content)
                ->scaleDown(width: 600)
                ->encode(new WebpEncoder(80));

            Storage::disk($disk)->put($thumb, (string) $webp, [
                'visibility'   => 'public',
                'ContentType'  => 'image/webp',
                'CacheControl' => 'max-age=31536000, immutable',
            ]);
        } catch (\Exception) {
            // Fall back to full image if generation fails
            return redirect(Storage::disk($disk)->url($image->path), 302);
        }

        return redirect(Storage::disk($disk)->url($thumb), 302);
    }
}
