<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotorcycleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function show(MotorcycleImage $image): RedirectResponse
    {
        if ($image->has_thumbnail) {
            $thumb = preg_replace('/\.webp$/', '_thumb.webp', $image->path);
            return redirect(Storage::disk(MotorcycleImage::disk())->url($thumb), 301);
        }

        return redirect($image->url, 302);
    }
}
