<x-admin-layout :heading="isset($motorcycle->id) ? 'Edit Motorcycle' : 'Add Motorcycle'">

    @php
        $action = isset($motorcycle->id)
            ? route('admin.motorcycles.update', $motorcycle)
            : route('admin.motorcycles.store');
    @endphp

    <div class="max-w-3xl">
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if(isset($motorcycle->id)) @method('PUT') @endif

            {{-- Core details --}}
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                <h2 class="font-semibold text-gray-800 border-b pb-2">Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make <span class="text-red-500">*</span></label>
                        <input type="text" name="make" value="{{ old('make', $motorcycle->make ?? '') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm @error('make') border-red-400 @enderror">
                        @error('make') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model <span class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model', $motorcycle->model ?? '') }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm @error('model') border-red-400 @enderror">
                        @error('model') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                        <input type="number" name="year" value="{{ old('year', $motorcycle->year ?? date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm @error('year') border-red-400 @enderror">
                        @error('year') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Colour</label>
                        <input type="text" name="color" value="{{ old('color', $motorcycle->color ?? '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Engine (cc)</label>
                        <input type="number" name="engine_size_cc" value="{{ old('engine_size_cc', $motorcycle->engine_size_cc ?? '') }}" min="1"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mileage (miles)</label>
                        <input type="number" name="mileage_miles" value="{{ old('mileage_miles', $motorcycle->mileage_miles ?? '') }}" min="0"
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 text-sm">$</span>
                            <input type="number" name="price" value="{{ old('price', $motorcycle->price ?? '') }}" min="0" step="0.01" required
                                   class="w-full pl-7 rounded-md border-gray-300 shadow-sm text-sm @error('price') border-red-400 @enderror">
                        </div>
                        @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $motorcycle->stock_quantity ?? 0) }}" min="0" required
                               class="w-full rounded-md border-gray-300 shadow-sm text-sm @error('stock_quantity') border-red-400 @enderror">
                        @error('stock_quantity') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="5"
                              class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('description', $motorcycle->description ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1"
                           @checked(old('is_featured', $motorcycle->is_featured ?? false))
                           class="rounded border-gray-300 text-indigo-600">
                    <label for="is_featured" class="text-sm text-gray-700">Featured (shown first in listing)</label>
                </div>
            </div>

            {{-- SEO --}}
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-800 border-b pb-2">SEO <span class="text-xs text-gray-400 font-normal">(optional overrides)</span></h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $motorcycle->meta_title ?? '') }}" maxlength="255"
                           placeholder="{{ ($motorcycle->year ?? date('Y')) . ' ' . ($motorcycle->make ?? 'Make') . ' ' . ($motorcycle->model ?? 'Model') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea name="meta_description" rows="2" maxlength="500"
                              class="w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('meta_description', $motorcycle->meta_description ?? '') }}</textarea>
                </div>
            </div>

            {{-- Images --}}
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-800 border-b pb-2">Images</h2>

                @if(isset($motorcycle->id) && $motorcycle->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($motorcycle->images as $img)
                            <div class="relative group">
                                <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-full aspect-square object-cover rounded-lg">
                                @if($img->is_primary)
                                    <span class="absolute top-1 left-1 text-xs bg-indigo-600 text-white px-1.5 py-0.5 rounded">Primary</span>
                                @else
                                    <button type="submit" form="set-primary-{{ $img->id }}"
                                            class="absolute top-1 left-1 hidden group-hover:block text-xs bg-white border text-gray-600 px-1.5 py-0.5 rounded hover:bg-indigo-50">Set Primary</button>
                                @endif
                                <button type="submit" form="delete-image-{{ $img->id }}"
                                        class="absolute top-1 right-1 hidden group-hover:block text-xs bg-red-500 text-white px-1.5 py-0.5 rounded hover:bg-red-600">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ isset($motorcycle->id) ? 'Add More Images' : 'Upload Images' }}
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-1">Max 5MB per image. First image will be set as primary.</p>
                    @error('images.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Video --}}
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                <h2 class="font-semibold text-gray-800 border-b pb-2">Video <span class="text-xs text-gray-400 font-normal">(optional)</span></h2>

                @if(isset($motorcycle->id) && $motorcycle->video_path)
                    <div class="space-y-3">
                        <video src="{{ $motorcycle->video_url }}" controls class="w-full rounded-lg max-h-56 bg-black"></video>
                        <button type="submit" form="remove-video-form"
                                class="text-xs text-red-600 hover:text-red-700 font-medium">Remove video</button>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ isset($motorcycle->id) && $motorcycle->video_path ? 'Replace Video' : 'Upload Video' }}
                    </label>
                    <input type="file" name="video" accept="video/mp4,video/webm,video/quicktime"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-1">Accepted: MP4, WebM, MOV. Max 200MB.</p>
                    @error('video') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center bg-indigo-600 text-white text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-indigo-700 transition-colors">
                    {{ isset($motorcycle->id) ? 'Update Motorcycle' : 'Add Motorcycle' }}
                </button>
                <a href="{{ route('admin.motorcycles.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            </div>
        </form>

        @if(isset($motorcycle->id))
            @foreach($motorcycle->images as $img)
                @if(!$img->is_primary)
                    <form id="set-primary-{{ $img->id }}" method="POST" action="{{ route('admin.images.primary', $img) }}">
                        @csrf @method('PATCH')
                    </form>
                @endif
                <form id="delete-image-{{ $img->id }}" method="POST" action="{{ route('admin.images.destroy', $img) }}"
                      onsubmit="return confirm('Remove this image?')">
                    @csrf @method('DELETE')
                </form>
            @endforeach

            @if($motorcycle->video_path)
                <form id="remove-video-form" method="POST" action="{{ route('admin.motorcycles.video.destroy', $motorcycle) }}"
                      onsubmit="return confirm('Remove this video?')">
                    @csrf @method('DELETE')
                </form>
            @endif
        @endif
    </div>

</x-admin-layout>
