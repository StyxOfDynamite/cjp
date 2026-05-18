<x-admin-layout :heading="isset($motorcycle->id) ? 'Edit Motorcycle' : 'Add Motorcycle'">

    @php
        $action = isset($motorcycle->id)
            ? route('admin.motorcycles.update', $motorcycle)
            : route('admin.motorcycles.store');
    @endphp

    <div class="max-w-3xl">
        <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if (isset($motorcycle->id))
                @method('PUT')
            @endif

            {{-- Core details --}}
            <div class="glass p-6 space-y-5">
                <h2 class="text-xs font-medium text-zinc-500 uppercase tracking-widest border-b border-white/10 pb-3">
                    Details</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Make <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="make" value="{{ old('make', $motorcycle->make ?? '') }}" required
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500 @error('make') border-red-500/50 @enderror">
                        @error('make')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Model <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="model" value="{{ old('model', $motorcycle->model ?? '') }}"
                            required
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500 @error('model') border-red-500/50 @enderror">
                        @error('model')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Year</label>
                        <input type="number" name="year" value="{{ old('year', $motorcycle->year ?? date('Y')) }}"
                            min="1900" max="{{ date('Y') + 1 }}"
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500 @error('year') border-red-500/50 @enderror">
                        @error('year')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label
                            class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Colour</label>
                        <input type="text" name="color" value="{{ old('color', $motorcycle->color ?? '') }}"
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Engine
                            (cc)</label>
                        <input type="number" name="engine_size_cc"
                            value="{{ old('engine_size_cc', $motorcycle->engine_size_cc ?? '') }}" min="1"
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Mileage
                            (miles)</label>
                        <input type="number" name="mileage_miles"
                            value="{{ old('mileage_miles', $motorcycle->mileage_miles ?? '') }}" min="0"
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Price <span
                                class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-zinc-500 text-sm">£</span>
                            <input type="number" name="price" value="{{ old('price', $motorcycle->price ?? '') }}"
                                min="0" step="0.01" required
                                class="w-full pl-7 bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500 @error('price') border-red-500/50 @enderror">
                        </div>
                        @error('price')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Stock
                            Quantity <span class="text-red-400">*</span></label>
                        <input type="number" name="stock_quantity"
                            value="{{ old('stock_quantity', $motorcycle->stock_quantity ?? 0) }}" min="0"
                            required
                            class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500 @error('stock_quantity') border-red-500/50 @enderror">
                        @error('stock_quantity')
                            <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label
                        class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Description</label>
                    <textarea name="description" rows="5"
                        class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('description', $motorcycle->description ?? '') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_featured" value="0">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1"
                        @checked(old('is_featured', $motorcycle->is_featured ?? false))
                        class="rounded-none border-white/20 bg-white/5 text-amber-500 focus:ring-amber-500">
                    <label for="is_featured" class="text-sm text-zinc-400">Featured (shown first in listing)</label>
                </div>
            </div>

            {{-- SEO --}}
            <div class="glass p-6 space-y-4">
                <h2 class="text-xs font-medium text-zinc-500 uppercase tracking-widest border-b border-white/10 pb-3">
                    SEO <span class="text-zinc-600 font-normal normal-case tracking-normal">(optional overrides)</span>
                </h2>
                <div>
                    <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Meta
                        Title</label>
                    <input type="text" name="meta_title"
                        value="{{ old('meta_title', $motorcycle->meta_title ?? '') }}" maxlength="255"
                        placeholder="{{ ($motorcycle->year ?? date('Y')) . ' ' . ($motorcycle->make ?? 'Make') . ' ' . ($motorcycle->model ?? 'Model') }}"
                        class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Meta
                        Description</label>
                    <textarea name="meta_description" rows="2" maxlength="500"
                        class="w-full bg-white/5 border-white/10 text-zinc-100 rounded-none shadow-none text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('meta_description', $motorcycle->meta_description ?? '') }}</textarea>
                </div>
            </div>

            {{-- Images --}}
            <div class="glass p-6 space-y-4">
                <h2 class="text-xs font-medium text-zinc-500 uppercase tracking-widest border-b border-white/10 pb-3">
                    Images</h2>

                @if (isset($motorcycle->id) && $motorcycle->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-3">
                        @foreach ($motorcycle->images as $img)
                            <div class="relative group">
                                @if ($img->has_thumbnail)
                                    <img src="{{ $img->url }}" alt="{{ $img->alt_text }}"
                                        class="w-full aspect-square object-cover border border-white/10">
                                @else
                                    <x-image-processing
                                        class="w-full aspect-square"
                                        :poll-url="route('admin.image.status', $img)"
                                        :alt="$img->alt_text"
                                    />
                                @endif
                                @if ($img->is_primary)
                                    <span
                                        class="absolute top-1 left-1 text-xs bg-amber-500 text-black px-1.5 py-0.5 font-semibold">Primary</span>
                                @else
                                    <button type="submit" form="set-primary-{{ $img->id }}"
                                        class="absolute top-1 left-1 hidden group-hover:block text-xs glass-light text-zinc-300 px-1.5 py-0.5 hover:text-white transition-colors">Set
                                        Primary</button>
                                @endif
                                <button type="submit" form="delete-image-{{ $img->id }}"
                                    class="absolute top-1 right-1 hidden group-hover:block text-xs bg-red-500/80 text-white px-1.5 py-0.5 hover:bg-red-500 transition-colors">&times;</button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">
                        {{ isset($motorcycle->id) ? 'Add More Images' : 'Upload Images' }}
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*"
                        class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-amber-500 file:text-black hover:file:bg-amber-400 file:transition-colors">
                    <p class="text-xs text-zinc-600 mt-1">Max 50MB per image. First image will be set as primary.</p>
                    @error('images.*')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Video --}}
            <div class="glass p-6 space-y-4">
                <h2 class="text-xs font-medium text-zinc-500 uppercase tracking-widest border-b border-white/10 pb-3">
                    Video <span class="text-zinc-600 font-normal normal-case tracking-normal">(optional)</span></h2>

                @if (isset($motorcycle->id) && $motorcycle->video_path)
                    <div class="space-y-3">
                        <video src="{{ $motorcycle->video_url }}" controls
                            class="w-full max-h-56 bg-black border border-white/10"></video>
                        <button type="submit" form="remove-video-form"
                            class="text-xs text-red-400 hover:text-red-300 font-medium transition-colors">Remove
                            video</button>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">
                        {{ isset($motorcycle->id) && $motorcycle->video_path ? 'Replace Video' : 'Upload Video' }}
                    </label>
                    <input type="file" name="video" accept="video/mp4,video/webm,video/quicktime"
                        class="block w-full text-sm text-zinc-400 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-amber-500 file:text-black hover:file:bg-amber-400 file:transition-colors">
                    <p class="text-xs text-zinc-600 mt-1">Accepted: MP4, WebM, MOV. Max 200MB.</p>
                    @error('video')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center bg-amber-500 text-black text-sm font-semibold px-6 py-2.5 hover:bg-amber-400 transition-colors">
                    {{ isset($motorcycle->id) ? 'Update Motorcycle' : 'Add Motorcycle' }}
                </button>
                <a href="{{ route('admin.motorcycles.index') }}"
                    class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">Cancel</a>
            </div>
        </form>

        @if (isset($motorcycle->id))
            @foreach ($motorcycle->images as $img)
                @if (!$img->is_primary)
                    <form id="set-primary-{{ $img->id }}" method="POST"
                        action="{{ route('admin.images.primary', $img) }}">
                        @csrf @method('PATCH')
                    </form>
                @endif
                <form id="delete-image-{{ $img->id }}" method="POST"
                    action="{{ route('admin.images.destroy', $img) }}"
                    onsubmit="return confirm('Remove this image?')">
                    @csrf @method('DELETE')
                </form>
            @endforeach

            @if ($motorcycle->video_path)
                <form id="remove-video-form" method="POST"
                    action="{{ route('admin.motorcycles.video.destroy', $motorcycle) }}"
                    onsubmit="return confirm('Remove this video?')">
                    @csrf @method('DELETE')
                </form>
            @endif
        @endif
    </div>

</x-admin-layout>
