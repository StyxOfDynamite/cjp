@php
    $canonical = route('motorcycles.make', $make);
    $pageTitle = $makeName . ' Motorcycles For Sale';
    $pageDescription = 'Browse ' . $motorcycles->total() . ' ' . $makeName . ' motorcycle' . ($motorcycles->total() === 1 ? '' : 's') . ' for sale at ' . config('seo.business_name') . ($inStockCount ? '. ' . $inStockCount . ' in stock' . ($priceFrom ? ' from £' . number_format($priceFrom, 2) : '') . '.' : '.');
@endphp

<x-app-layout
    :title="$pageTitle"
    :description="$pageDescription"
    :canonical="$canonical"
>
    @push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $pageTitle,
            'description' => $pageDescription,
            'url' => $canonical,
            'numberOfItems' => $motorcycles->total(),
            'itemListElement' => $motorcycles->map(fn ($bike, $i) => [
                '@type' => 'ListItem',
                'position' => (($motorcycles->currentPage() - 1) * $motorcycles->perPage()) + $i + 1,
                'url' => route('motorcycles.show', $bike),
                'name' => trim("{$bike->year} {$bike->make} {$bike->model}"),
            ])->values()->all(),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <nav class="text-sm text-zinc-500 mb-6 flex items-center gap-1">
            <a href="{{ route('motorcycles.index') }}" class="hover:text-amber-400 transition-colors">Motorcycles</a>
            <span class="text-zinc-700">/</span>
            <span class="text-zinc-300">{{ $makeName }}</span>
        </nav>

        {{-- Brand hero --}}
        <div class="glass p-8 mb-8 border-l-4 border-amber-500">
            <h1 class="text-4xl font-bold tracking-tight text-white">{{ $makeName }} Motorcycles</h1>
            <p class="mt-2 text-zinc-400 text-lg">For sale at {{ config('seo.business_name') }}</p>

            <div class="mt-6 flex flex-wrap gap-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">Total Listings</p>
                    <p class="text-2xl font-bold text-white mt-0.5">{{ $motorcycles->total() }}</p>
                </div>
                @if($inStockCount)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">In Stock</p>
                    <p class="text-2xl font-bold text-green-400 mt-0.5">{{ $inStockCount }}</p>
                </div>
                @endif
                @if($priceFrom)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500">From</p>
                    <p class="text-2xl font-bold text-amber-400 mt-0.5">£{{ number_format($priceFrom, 2) }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Bike grid --}}
        @if($motorcycles->isEmpty())
            <div class="text-center py-20 text-zinc-500">
                <p class="text-lg">No {{ $makeName }} motorcycles listed right now.</p>
                <a href="{{ route('motorcycles.index') }}" class="mt-2 inline-block text-amber-400 hover:text-amber-300 text-sm">Browse all motorcycles</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($motorcycles as $bike)
                    @php $primary = $bike->images->first(); @endphp
                    @if($loop->first && $primary)
                        @push('head')
                            <link rel="preload" as="image" href="{{ $primary->url }}">
                        @endpush
                    @endif
                    <a href="{{ route('motorcycles.show', $bike) }}" class="group glass overflow-hidden hover:bg-white/10 hover:border-white/20 transition-all duration-200">
                        <div class="aspect-video bg-zinc-900 overflow-hidden">
                            @if($primary)
                                <img src="{{ $loop->first ? $primary->url : $primary->thumbnail_url }}"
                                     alt="{{ $primary->alt_text ?? $bike->year . ' ' . $bike->make . ' ' . $bike->model }}"
                                     loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}"
                                     fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                                     decoding="{{ $loop->first ? 'sync' : 'async' }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-90 group-hover:opacity-100">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-zinc-700">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($bike->is_featured)
                                <span class="inline-block text-xs font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5 mb-2">Featured</span>
                            @endif
                            <h2 class="font-semibold text-zinc-100 group-hover:text-white transition-colors">
                                {{ $bike->year }} {{ $bike->make }} {{ $bike->model }}
                            </h2>
                            <div class="mt-1 flex items-center gap-3 text-sm text-zinc-400">
                                @if($bike->engine_size_cc) <span>{{ $bike->engine_size_cc }}cc</span> @endif
                                @if($bike->mileage_miles) <span>{{ number_format($bike->mileage_miles) }} mi</span> @endif
                                @if($bike->color) <span>{{ $bike->color }}</span> @endif
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-amber-400">£{{ number_format($bike->price, 2) }}</span>
                                <span class="text-xs px-2 py-1 rounded-full {{ $bike->isInStock() ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                    {{ $bike->isInStock() ? 'In Stock' : 'Sold' }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $motorcycles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
