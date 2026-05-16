<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateThumbnail;
use App\Models\MotorcycleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function show(MotorcycleImage $image): RedirectResponse
    {
        $disk  = MotorcycleImage::disk();
        $thumb = preg_replace('/\.webp$/', '_thumb.webp', $image->path);

        if (Storage::disk($disk)->exists($thumb)) {
            // Thumbnail exists — permanent redirect, browser caches and bypasses Laravel next time
            return redirect(Storage::disk($disk)->url($thumb), 301);
        }

        // Dispatch background job to generate it, serve full image in the meantime
        GenerateThumbnail::dispatch($image);

        return redirect(Storage::disk($disk)->url($image->path), 302);
    }
}
