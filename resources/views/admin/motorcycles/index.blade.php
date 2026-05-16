<x-admin-layout heading="Motorcycles">

    <p class="text-sm text-zinc-500 mb-4">{{ $motorcycles->total() }} motorcycle(s) total</p>

    <form method="GET" action="{{ route('admin.motorcycles.index') }}" class="mb-6 glass p-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Search name, make, model, description…"
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
            <div class="flex items-center gap-1">
                <span class="text-zinc-600 text-sm">£</span>
                <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}"
                       placeholder="Min price" min="0" step="0.01"
                       class="w-28 bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500">
                <span class="text-zinc-600 text-sm">–</span>
                <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}"
                       placeholder="Max price" min="0" step="0.01"
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

    <div class="glass overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10 text-sm whitespace-nowrap">
            <thead class="bg-white/5">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Motorcycle</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wide">Featured</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($motorcycles as $bike)
                    <tr class="hover:bg-white/5 transition-colors" x-data="{ editing: false, qty: {{ $bike->stock_quantity }} }">
                        <td class="px-4 py-3 flex items-center gap-3">
                            @php $img = $bike->images->first(); @endphp
                            <a href="{{ route('motorcycles.show', $bike) }}" target="_blank" class="flex-shrink-0">
                                @if($img)
                                    <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-12 h-10 object-cover hover:opacity-80 transition-opacity">
                                @else
                                    <div class="w-12 h-10 bg-white/5 border border-white/10"></div>
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('motorcycles.show', $bike) }}" target="_blank"
                                   class="font-medium text-zinc-100 hover:text-amber-400 transition-colors">{{ $bike->year }} {{ $bike->make }} {{ $bike->model }}</a>
                                @if($bike->color)<p class="text-xs text-zinc-600">{{ $bike->color }}</p>@endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-300">£{{ number_format($bike->price, 2) }}</td>
                        <td class="px-4 py-3">
                            <div x-show="!editing" class="flex items-center gap-2">
                                <span class="font-medium {{ $bike->stock_quantity > 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $bike->stock_quantity }}
                                </span>
                                <button @click="editing = true" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">edit</button>
                            </div>
                            <form x-show="editing" method="POST" action="{{ route('admin.motorcycles.stock', $bike) }}" class="flex items-center gap-1">
                                @csrf @method('PATCH')
                                <input type="number" name="stock_quantity" x-model="qty" min="0"
                                       class="w-20 bg-white/5 border-white/10 text-zinc-100 text-sm px-2 py-1 rounded-none">
                                <button type="submit" class="text-xs bg-amber-500 text-black px-2 py-1 hover:bg-amber-400 transition-colors">Save</button>
                                <button type="button" @click="editing = false" class="text-xs text-zinc-600 hover:text-zinc-300 transition-colors">Cancel</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            @if($bike->is_featured)
                                <span class="text-xs bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5">Featured</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('motorcycles.show', $bike) }}" target="_blank" class="text-xs text-zinc-600 hover:text-zinc-300 mr-2 transition-colors">View</a>
                            <a href="{{ route('admin.motorcycles.edit', $bike) }}" class="text-xs text-amber-400 hover:text-amber-300 mr-2 transition-colors">Edit</a>
                            <form method="POST" action="{{ route('admin.motorcycles.destroy', $bike) }}" class="inline"
                                  onsubmit="return confirm('Delete this motorcycle?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-600">
                            No motorcycles yet. <a href="{{ route('admin.motorcycles.create') }}" class="text-amber-400 hover:text-amber-300">Add the first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $motorcycles->links() }}</div>

</x-admin-layout>
