<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateThumbnail;
use App\Models\MotorcycleImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function status(MotorcycleImage $image): JsonResponse
    {
        $image->refresh();

        return response()->json([
            'ready' => $image->has_thumbnail,
            'url'   => $image->has_thumbnail ? $image->url : null,
        ]);
    }

    public function show(MotorcycleImage $image): RedirectResponse
    {
        Log::info("[thumbnail] requested image={$image->id} has_thumbnail={$image->has_thumbnail}");

        if ($image->has_thumbnail) {
            $thumb = preg_replace('/\.webp$/', '_thumb.webp', $image->path);
            return redirect(Storage::disk(MotorcycleImage::disk())->url($thumb), 301);
        }

        Log::info("[thumbnail] dispatching job image={$image->id} path={$image->path}");
        GenerateThumbnail::dispatch($image);

        return redirect($image->url, 302);
    }
}
