@php
    $primaryImage = $motorcycle->images->where('is_primary', true)->first() ?? $motorcycle->images->first();
    $canonical = route('motorcycles.show', $motorcycle);
@endphp

<x-app-layout
    :title="$motorcycle->seo_title"
    :description="$motorcycle->seo_description"
    :canonical="$canonical"
    :image="$primaryImage?->url"
    type="product"
>
    @push('head')
    @php
        $jsonLd = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Motorcycle',
            'name' => "{$motorcycle->year} {$motorcycle->make} {$motorcycle->model}",
            'description' => $motorcycle->description ?: null,
            'image' => $motorcycle->images->pluck('url')->values()->all() ?: null,
            'video' => $motorcycle->video_path ? $motorcycle->video_url : null,
            'brand' => ['@type' => 'Brand', 'name' => $motorcycle->make],
            'color' => $motorcycle->color ?: null,
            'vehicleModelDate' => $motorcycle->year ? (string) $motorcycle->year : null,
            'engineDisplacement' => $motorcycle->engine_size_cc ? [
                '@type' => 'QuantitativeValue',
                'value' => $motorcycle->engine_size_cc,
                'unitCode' => 'CMQ',
            ] : null,
            'mileageFromOdometer' => $motorcycle->mileage_miles !== null ? [
                '@type' => 'QuantitativeValue',
                'value' => $motorcycle->mileage_miles,
                'unitCode' => 'SMI',
            ] : null,
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format($motorcycle->price, 2, '.', ''),
                'priceCurrency' => 'GBP',
                'availability' => $motorcycle->isInStock()
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'url' => $canonical,
                'seller' => [
                    '@type' => 'MotorcycleDealer',
                    'name' => config('seo.business_name'),
                    'url' => config('app.url'),
                ],
            ],
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-1">
            <a href="{{ route('motorcycles.index') }}" class="hover:text-amber-400 transition-colors">Motorcycles</a>
            <span class="text-zinc-700">/</span>
            <span class="text-zinc-300">{{ $motorcycle->year }} {{ $motorcycle->make }} {{ $motorcycle->model }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            {{-- Image gallery --}}
            <div
                x-data="{
                    images: @js($motorcycle->images->map(fn ($i) => [
                        'url' => $i->thumbnail_url,
                        'alt' => $i->alt_text ?? '',
                    ])->values()),
                    active: 0,
                    open: false,
                    prev() { this.active = this.active > 0 ? this.active - 1 : this.images.length - 1 },
                    next() { this.active = this.active < this.images.length - 1 ? this.active + 1 : 0 },
                }"
                x-effect="document.body.style.overflow = open ? 'hidden' : ''"
                x-on:keydown.escape.window="open = false"
                x-on:keydown.arrow-left.window="if (open) prev()"
                x-on:keydown.arrow-right.window="if (open) next()"
            >
                @if($motorcycle->images->isNotEmpty())
                    <div class="glass overflow-hidden aspect-video mb-3"
                         :class="images.length > 1 ? 'cursor-zoom-in' : ''"
                         x-on:click="if (images.length > 1) open = true">
                        <img :src="images[active].url" :alt="images[active].alt" class="w-full h-full object-cover">
                    </div>

                    @if($motorcycle->images->count() > 1)
                        <div class="grid grid-cols-5 gap-2">
                            <template x-for="(img, i) in images" :key="i">
                                <button x-on:click="active = i"
                                        :class="active === i ? 'ring-amber-400 ring-2' : 'ring-white/10 ring-1 hover:ring-amber-400/50'"
                                        class="aspect-square overflow-hidden bg-zinc-900 focus:outline-none transition-all">
                                    <img :src="img.url" :alt="img.alt" loading="lazy" class="w-full h-full object-cover opacity-80 hover:opacity-100 transition-opacity">
                                </button>
                            </template>
                        </div>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-on:click.self="open = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/95"
                            style="display:none"
                        >
                            <button x-on:click="open = false"
                                    class="absolute top-4 right-4 p-2 text-white/60 hover:text-white transition-colors">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <button x-on:click="prev()"
                                    class="absolute left-4 p-2 text-white/60 hover:text-white transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <img :src="images[active].url" :alt="images[active].alt"
                                 class="max-h-[90vh] max-w-[90vw] object-contain select-none">
                            <button x-on:click="next()"
                                    class="absolute right-4 p-2 text-white/60 hover:text-white transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/50 text-sm tabular-nums"
                                 x-text="`${active + 1} / ${images.length}`"></div>
                        </div>
                    @endif
                @else
                    <div class="glass aspect-video flex items-center justify-center text-zinc-700">
                        <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="glass p-8">
                @if($motorcycle->is_featured)
                    <span class="inline-block text-xs font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5 mb-4">Featured</span>
                @endif

                <h1 class="text-3xl font-bold tracking-tight text-white">
                    {{ $motorcycle->year }} {{ $motorcycle->make }} {{ $motorcycle->model }}
                </h1>

                <div class="mt-4 flex items-center gap-3">
                    <span class="text-3xl font-bold text-amber-400">£{{ number_format($motorcycle->price, 2) }}</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $motorcycle->isInStock() ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                        {{ $motorcycle->isInStock() ? 'In Stock (' . $motorcycle->stock_quantity . ' available)' : 'Sold' }}
                    </span>
                </div>

                <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-3 text-sm border-t border-white/10 pt-6">
                    @if($motorcycle->year)
                        <div><dt class="text-zinc-500 text-xs uppercase tracking-wide">Year</dt><dd class="font-medium text-white mt-0.5">{{ $motorcycle->year }}</dd></div>
                    @endif
                    @if($motorcycle->color)
                        <div><dt class="text-zinc-500 text-xs uppercase tracking-wide">Colour</dt><dd class="font-medium text-white mt-0.5">{{ $motorcycle->color }}</dd></div>
                    @endif
                    @if($motorcycle->engine_size_cc)
                        <div><dt class="text-zinc-500 text-xs uppercase tracking-wide">Engine</dt><dd class="font-medium text-white mt-0.5">{{ $motorcycle->engine_size_cc }}cc</dd></div>
                    @endif
                    @if($motorcycle->mileage_miles !== null)
                        <div><dt class="text-zinc-500 text-xs uppercase tracking-wide">Mileage</dt><dd class="font-medium text-white mt-0.5">{{ number_format($motorcycle->mileage_miles) }} mi</dd></div>
                    @endif
                </dl>

                @if($motorcycle->description)
                    <div class="mt-6 text-sm text-zinc-300 leading-relaxed border-t border-white/10 pt-6">
                        {!! nl2br(e($motorcycle->description)) !!}
                    </div>
                @endif

                <div class="mt-8 glass-light p-4 text-sm text-zinc-300">
                    <p class="font-semibold text-white mb-1">Enquire about this bike</p>
                    <p>Call us at <a href="tel:{{ config('seo.phone') }}" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">{{ config('seo.phone') }}</a></p>
                    <p class="mt-1 text-xs text-zinc-500">{{ config('seo.address_street') }}, {{ config('seo.address_city') }}</p>
                </div>

                @php
                    $shareTitle = urlencode("{$motorcycle->year} {$motorcycle->make} {$motorcycle->model} — " . config('seo.business_name'));
                    $shareUrl   = urlencode($canonical);
                    $shareText  = urlencode("{$motorcycle->year} {$motorcycle->make} {$motorcycle->model} for £{$motorcycle->price} — check it out!");
                @endphp
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-xs text-zinc-600 mr-1">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"
                       class="inline-flex items-center justify-center w-8 h-8 bg-[#1877F2] text-white hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987H7.898V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="https://x.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on X"
                       class="inline-flex items-center justify-center w-8 h-8 bg-black border border-white/20 text-white hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp"
                       class="inline-flex items-center justify-center w-8 h-8 bg-[#25D366] text-white hover:opacity-90 transition-opacity">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                    </a>
                </div>
            </div>
        </div>

        @if($motorcycle->video_path)
            <div class="mt-10">
                <h2 class="text-lg font-semibold text-white tracking-tight mb-3">Video</h2>
                <video src="{{ $motorcycle->video_url }}" controls
                       class="w-full max-w-3xl bg-black">
                </video>
            </div>
        @endif
    </div>
</x-app-layout>
