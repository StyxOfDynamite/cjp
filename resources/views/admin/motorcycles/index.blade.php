<x-admin-layout heading="Motorcycles">

    <p class="text-sm text-zinc-500 mb-4">{{ $motorcycles->total() }} motorcycle(s) total</p>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.motorcycles.index') }}" class="mb-6 glass p-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Search make, model, description…"
                       class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div class="flex items-center gap-1">
                <input type="number" name="year_min" value="{{ $filters['year_min'] ?? '' }}"
                       placeholder="Year from" min="1900" max="{{ date('Y') + 1 }}"
                       class="w-28 bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500">
                <span class="text-zinc-600 text-sm">–</span>
                <input type="number" name="year_max" value="{{ $filters['year_max'] ?? '' }}"
                       placeholder="Year to" min="1900" max="{{ date('Y') + 1 }}"
                       class="w-28 bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1.5 text-sm text-zinc-400 cursor-pointer select-none">
                    <input type="checkbox" name="in_stock" value="1"
                           {{ !empty($filters['in_stock']) ? 'checked' : '' }}
                           class="rounded-none border-white/20 bg-white/5 text-amber-500 focus:ring-amber-500">
                    In stock only
                </label>
                <button type="submit"
                        class="bg-amber-500 text-black text-sm font-semibold px-4 py-2 hover:bg-amber-400 transition-colors">
                    Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('admin.motorcycles.index') }}"
                       class="text-sm text-zinc-500 hover:text-zinc-300 px-2 py-2 transition-colors">
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Card grid --}}
    @if($motorcycles->isEmpty())
        <div class="glass px-6 py-16 text-center text-zinc-600">
            No motorcycles yet.
            <a href="{{ route('admin.motorcycles.create') }}" class="text-amber-400 hover:text-amber-300">Add the first one.</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($motorcycles as $bike)
                @php $img = $bike->images->first(); @endphp
                <div class="glass overflow-hidden"
                     x-data="{ menu: false, editing: false, qty: {{ $bike->stock_quantity }} }"
                     @click.outside="menu = false">

                    {{-- Image --}}
                    <div class="relative aspect-video bg-zinc-900">
                        @if($img)
                            <img src="{{ $img->has_thumbnail ? route('admin.image.thumbnail', $img) : $img->url }}" alt="{{ $img->alt_text }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover opacity-90">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-700">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Badges --}}
                        <div class="absolute top-2 left-2 flex gap-1">
                            @if($bike->is_featured)
                                <span class="text-xs font-semibold bg-amber-500/90 text-black px-2 py-0.5">Featured</span>
                            @endif
                            <span class="text-xs font-semibold px-2 py-0.5 {{ $bike->stock_quantity > 0 ? 'bg-green-500/90 text-black' : 'bg-zinc-800 text-zinc-400' }}">
                                {{ $bike->stock_quantity > 0 ? 'In Stock' : 'Sold' }}
                            </span>
                        </div>

                        {{-- 3-dot context menu --}}
                        <div class="absolute top-2 right-2">
                            <button @click.stop="menu = !menu"
                                    class="w-8 h-8 flex items-center justify-center bg-black/50 hover:bg-black/70 text-white transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                </svg>
                            </button>

                            <div x-show="menu"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 top-9 w-36 bg-zinc-800 border border-white/10 z-10 py-1"
                                 style="display:none">
                                <a href="{{ route('motorcycles.show', $bike) }}" target="_blank"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/10 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    View
                                </a>
                                <a href="{{ route('admin.motorcycles.edit', $bike) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-zinc-300 hover:text-white hover:bg-white/10 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <div class="border-t border-white/10 my-1"></div>
                                <form method="POST" action="{{ route('admin.motorcycles.destroy', $bike) }}"
                                      onsubmit="return confirm('Delete {{ addslashes($bike->year . ' ' . $bike->make . ' ' . $bike->model) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-white/10 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <h3 class="font-semibold text-zinc-100 leading-snug">
                            {{ $bike->year }} {{ $bike->make }} {{ $bike->model }}
                        </h3>
                        <div class="mt-1 flex items-center gap-2 text-xs text-zinc-500">
                            @if($bike->color)<span>{{ $bike->color }}</span>@endif
                            @if($bike->engine_size_cc)<span>&middot; {{ $bike->engine_size_cc }}cc</span>@endif
                            @if($bike->mileage_miles)<span>&middot; {{ number_format($bike->mileage_miles) }} mi</span>@endif
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-amber-400 font-bold">£{{ number_format($bike->price, 2) }}</span>

                            {{-- Inline stock editor --}}
                            <div class="flex items-center gap-2">
                                <div x-show="!editing" class="flex items-center gap-1.5">
                                    <span class="text-xs {{ $bike->stock_quantity > 0 ? 'text-green-400' : 'text-zinc-500' }}">
                                        Qty: {{ $bike->stock_quantity }}
                                    </span>
                                    <button @click="editing = true"
                                            class="text-xs text-zinc-600 hover:text-amber-400 transition-colors">edit</button>
                                </div>
                                <form x-show="editing" method="POST" action="{{ route('admin.motorcycles.stock', $bike) }}"
                                      class="flex items-center gap-1" style="display:none">
                                    @csrf @method('PATCH')
                                    <input type="number" name="stock_quantity" x-model="qty" min="0"
                                           class="w-16 bg-white/5 border-white/10 text-zinc-100 text-xs px-2 py-1 rounded-none">
                                    <button type="submit"
                                            class="text-xs bg-amber-500 text-black px-2 py-1 hover:bg-amber-400 transition-colors">✓</button>
                                    <button type="button" @click="editing = false"
                                            class="text-xs text-zinc-600 hover:text-zinc-300 transition-colors">✕</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6">{{ $motorcycles->links() }}</div>

</x-admin-layout>
