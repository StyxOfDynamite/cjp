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
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Motorcycles For Sale</h1>

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Filter sidebar --}}
            <aside class="lg:w-64 flex-shrink-0">
                <form method="GET" action="{{ route('motorcycles.index') }}" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
                    <h2 class="font-semibold text-gray-800">Filter</h2>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Make</label>
                        <select name="make" class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                            <option value="">All Makes</option>
                            @foreach($makes as $make)
                                <option value="{{ $make }}" @selected(($filters['make'] ?? '') === $make)>{{ $make }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Model</label>
                        <input type="text" name="model" value="{{ $filters['model'] ?? '' }}" placeholder="e.g. CB500F"
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Year from</label>
                            <input type="number" name="year_min" value="{{ $filters['year_min'] ?? '' }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="2000"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Year to</label>
                            <input type="number" name="year_max" value="{{ $filters['year_max'] ?? '' }}" min="1900" max="{{ date('Y') + 1 }}" placeholder="{{ date('Y') }}"
                                   class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Price</label>
                        <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}" min="0" placeholder="Any"
                               class="w-full rounded-md border-gray-300 text-sm shadow-sm">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="in_stock" name="in_stock" value="1" @checked(! empty($filters['in_stock']))
                               class="rounded border-gray-300 text-indigo-600">
                        <label for="in_stock" class="text-sm text-gray-700">In Stock Only</label>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white text-sm font-medium py-2 rounded-md hover:bg-indigo-700">
                            Apply
                        </button>
                        <a href="{{ route('motorcycles.index') }}" class="flex-1 text-center bg-gray-100 text-gray-700 text-sm font-medium py-2 rounded-md hover:bg-gray-200">
                            Clear
                        </a>
                    </div>
                </form>
            </aside>

            {{-- Listing grid --}}
            <div class="flex-1">
                @if($motorcycles->isEmpty())
                    <div class="text-center py-20 text-gray-400">
                        <p class="text-lg">No motorcycles match your search.</p>
                        <a href="{{ route('motorcycles.index') }}" class="mt-2 inline-block text-indigo-600 hover:underline text-sm">Clear filters</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($motorcycles as $bike)
                            @php $primary = $bike->images->first(); @endphp
                            <a href="{{ route('motorcycles.show', $bike) }}" class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                <div class="aspect-video bg-gray-100 overflow-hidden">
                                    @if($primary)
                                        <img src="{{ $primary->url }}" alt="{{ $primary->alt_text ?? $bike->year . ' ' . $bike->make . ' ' . $bike->model }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    @if($bike->is_featured)
                                        <span class="inline-block text-xs font-semibold bg-amber-100 text-amber-800 px-2 py-0.5 rounded mb-2">Featured</span>
                                    @endif
                                    <h2 class="font-semibold text-gray-900 group-hover:text-indigo-600">
                                        {{ $bike->year }} {{ $bike->make }} {{ $bike->model }}
                                    </h2>
                                    <div class="mt-1 flex items-center gap-3 text-sm text-gray-500">
                                        @if($bike->engine_size_cc) <span>{{ $bike->engine_size_cc }}cc</span> @endif
                                        @if($bike->mileage_miles) <span>{{ number_format($bike->mileage_miles) }} mi</span> @endif
                                        @if($bike->color) <span>{{ $bike->color }}</span> @endif
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-lg font-bold text-gray-900">£{{ number_format($bike->price, 2) }}</span>
                                        <span class="text-xs px-2 py-1 rounded-full {{ $bike->isInStock() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
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
