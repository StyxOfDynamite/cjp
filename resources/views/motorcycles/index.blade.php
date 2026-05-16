<x-app-layout>
    @php
        $title = 'Motorcycles For Sale';
        $description = 'Browse our full range of motorcycles in stock. Filter by make, model, year and price.';
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => 'Motorcycles For Sale',
            'numberOfItems' => $motorcycles->total(),
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-bold tracking-tight text-white mb-8">Motorcycles For Sale</h1>

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Filter sidebar --}}
            <aside class="lg:w-64 flex-shrink-0">
                <form method="GET" action="{{ route('motorcycles.index') }}" class="glass p-6 space-y-5">
                    <h2 class="font-semibold text-zinc-300 text-xs uppercase tracking-widest">Filter</h2>

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Make</label>
                        <select name="make" class="w-full bg-white/5 border-white/10 text-zinc-100 text-sm shadow-none rounded-none focus:border-amber-500 focus:ring-amber-500">
                            <option value="" class="bg-zinc-900">All Makes</option>
                            @foreach($makes as $make)
                                <option value="{{ $make }}" class="bg-zinc-900" @selected(($filters['make'] ?? '') === $make)>{{ $make }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Model</label>
                        <input type="text" name="model" value="{{ $filters['model'] ?? '' }}" placeholder="e.g. CB500F"
                               class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Year from</label>
                            <input type="number" name="year_min" value="{{ $filters['year_min'] ?? '' }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="2000"
                                   class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Year to</label>
                            <input type="number" name="year_max" value="{{ $filters['year_max'] ?? '' }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}"
                                   class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 uppercase tracking-wide mb-1">Max Price</label>
                        <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" min="0" placeholder="Any"
                               class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm rounded-none focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="in_stock" name="in_stock" value="1" @checked(! empty($filters['in_stock']))
                               class="rounded-none border-white/20 bg-white/5 text-amber-500 focus:ring-amber-500">
                        <label for="in_stock" class="text-sm text-zinc-400">In Stock Only</label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-amber-500 text-black text-sm font-semibold py-2 hover:bg-amber-400 transition-colors">
                            Apply
                        </button>
                        <a href="{{ route('motorcycles.index') }}" class="flex-1 text-center bg-white/5 border border-white/10 text-zinc-400 text-sm font-medium py-2 hover:bg-white/10 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </aside>

            {{-- Listing grid --}}
            <div class="flex-1">
                @if($motorcycles->isEmpty())
                    <div class="text-center py-20 text-zinc-500">
                        <p class="text-lg">No motorcycles match your search.</p>
                        <a href="{{ route('motorcycles.index') }}" class="mt-2 inline-block text-amber-400 hover:text-amber-300 text-sm">Clear filters</a>
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
                                        <img src="{{ $primary->url }}" alt="{{ $primary->alt_text ?? $bike->year . ' ' . $bike->make . ' ' . $bike->model }}"
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
                                            {{ $bike->isInStock() ? 'In Stock' : 'Out of Stock' }}
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
        </div>
    </div>
</x-app-layout>
