<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateThumbnail;
use App\Models\Motorcycle;
use App\Models\MotorcycleImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'year_min', 'year_max', 'price_min', 'price_max', 'in_stock']);

        $motorcycles = Motorcycle::with(['images' => fn($q) => $q->where('is_primary', true)])
            ->filter($filters)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.motorcycles.index', compact('motorcycles', 'filters'));
    }

    public function create(): View
    {
        return view('admin.motorcycles.form', ['motorcycle' => new Motorcycle]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $motorcycle = Motorcycle::create($data);
        $this->handleImages($request, $motorcycle);
        $this->handleVideo($request, $motorcycle);

        return redirect()->route('admin.motorcycles.index')->with('success', 'Motorcycle added.');
    }

    public function edit(Motorcycle $motorcycle): View
    {
        $motorcycle->load('images');

        return view('admin.motorcycles.form', compact('motorcycle'));
    }

    public function update(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $data = $this->validated($request);
        $motorcycle->update($data);
        $this->handleImages($request, $motorcycle);
        $this->handleVideo($request, $motorcycle);

        return redirect()->route('admin.motorcycles.index')->with('success', 'Motorcycle updated.');
    }

    public function destroy(Motorcycle $motorcycle): RedirectResponse
    {
        $disk = MotorcycleImage::disk();
        foreach ($motorcycle->images as $image) {
            Storage::disk($disk)->delete($image->path);
        }
        if ($motorcycle->video_path) {
            Storage::disk($disk)->delete($motorcycle->video_path);
        }
        $motorcycle->delete();

        return redirect()->route('admin.motorcycles.index')->with('success', 'Motorcycle deleted.');
    }

    public function destroyVideo(Motorcycle $motorcycle): RedirectResponse
    {
        if ($motorcycle->video_path) {
            Storage::disk(MotorcycleImage::disk())->delete($motorcycle->video_path);
            $motorcycle->update(['video_path' => null]);
        }

        return back()->with('success', 'Video removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'make'            => 'required|string|max:100',
            'model'           => 'required|string|max:100',
            'year'            => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color'           => 'nullable|string|max:50',
            'engine_size_cc'  => 'nullable|integer|min:1',
            'mileage_miles'      => 'nullable|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'stock_quantity'  => 'required|integer|min:0',
            'description'     => 'nullable|string',
            'meta_title'      => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_featured'     => 'boolean',
        ]);
    }

    private function handleImages(Request $request, Motorcycle $motorcycle): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $request->validate([
            'images.*' => [
                File::image()->min('10kb')->max('15mb')
            ]
        ]);

        $disk    = MotorcycleImage::disk();
        $isFirst = $motorcycle->images()->count() === 0;

        foreach ($request->file('images') as $i => $file) {
            $uuid   = Str::uuid();
            $path   = "motorcycles/{$motorcycle->id}/{$uuid}.webp";
            $source = "motorcycles/{$motorcycle->id}/{$uuid}.source";

            Storage::disk($disk)->put($source, $file->getContent(), ['visibility' => 'public']);

            $image = $motorcycle->images()->create([
                'path'          => $path,
                'alt_text'      => "{$motorcycle->year} {$motorcycle->make} {$motorcycle->model}",
                'sort_order'    => $motorcycle->images()->count() + $i,
                'is_primary'    => $isFirst && $i === 0,
                'has_thumbnail' => false,
            ]);

            GenerateThumbnail::dispatch($image);
        }
    }

    private function handleVideo(Request $request, Motorcycle $motorcycle): void
    {
        if (! $request->hasFile('video')) {
            return;
        }

        $request->validate(['video' => 'file|mimes:mp4,webm,mov|max:204800']);

        $disk = MotorcycleImage::disk();

        if ($motorcycle->video_path) {
            Storage::disk($disk)->delete($motorcycle->video_path);
        }

        $content = $request->file('video')->get();
        $ext = $request->file('video')->extension();
        $path = "motorcycles/{$motorcycle->id}/video/" . Str::uuid() . ".{$ext}";
        Storage::disk($disk)->put($path, $content, [
            'visibility'  => 'public',
            'ContentType' => $request->file('video')->getMimeType(),
        ]);
        $motorcycle->update(['video_path' => $path]);
    }

    public function destroyImage(MotorcycleImage $image): RedirectResponse
    {
        Storage::disk(MotorcycleImage::disk())->delete($image->path);

        $wasPrimary = $image->is_primary;
        $motorcycleId = $image->motorcycle_id;
        $image->delete();

        if ($wasPrimary) {
            MotorcycleImage::where('motorcycle_id', $motorcycleId)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function setPrimaryImage(MotorcycleImage $image): RedirectResponse
    {
        MotorcycleImage::where('motorcycle_id', $image->motorcycle_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }
}
