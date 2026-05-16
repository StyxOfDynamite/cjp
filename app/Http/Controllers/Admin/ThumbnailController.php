<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateThumbnail;
use App\Models\MotorcycleImage;
use Illuminate\Http\RedirectResponse;

class ThumbnailController extends Controller
{
    public function show(MotorcycleImage $image): RedirectResponse
    {
        GenerateThumbnail::dispatch($image);

        return redirect($image->url, 302);
    }
}
