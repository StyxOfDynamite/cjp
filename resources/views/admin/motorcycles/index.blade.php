<x-admin-layout heading="Motorcycles">

    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">{{ $motorcycles->total() }} motorcycle(s) total</p>
        <a href="{{ route('admin.motorcycles.create') }}"
           class="inline-flex items-center gap-1 bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Add Motorcycle
        </a>
    </div>

    <form method="GET" action="{{ route('admin.motorcycles.index') }}" class="mb-6 bg-white rounded-xl shadow-sm p-4">
        <div class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-48">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Search name, make, model, description…"
                       class="w-full rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-center gap-1">
                <input type="number" name="year_min" value="{{ $filters['year_min'] ?? '' }}"
                       placeholder="Year from" min="1900" max="{{ date('Y') + 1 }}"
                       class="w-28 rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <span class="text-gray-400 text-sm">–</span>
                <input type="number" name="year_max" value="{{ $filters['year_max'] ?? '' }}"
                       placeholder="Year to" min="1900" max="{{ date('Y') + 1 }}"
                       class="w-28 rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-center gap-1">
                <span class="text-gray-400 text-sm">£</span>
                <input type="number" name="price_min" value="{{ $filters['price_min'] ?? '' }}"
                       placeholder="Min price" min="0" step="0.01"
                       class="w-28 rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <span class="text-gray-400 text-sm">–</span>
                <input type="number" name="price_max" value="{{ $filters['price_max'] ?? '' }}"
                       placeholder="Max price" min="0" step="0.01"
                       class="w-28 rounded-lg border-gray-300 text-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-1.5 text-sm text-gray-600 cursor-pointer select-none">
                    <input type="checkbox" name="in_stock" value="1"
                           {{ !empty($filters['in_stock']) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    In stock only
                </label>
                <button type="submit"
                        class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Filter
                </button>
                @if(array_filter($filters))
                    <a href="{{ route('admin.motorcycles.index') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 px-2 py-2">
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motorcycle</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Featured</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($motorcycles as $bike)
                    <tr class="hover:bg-gray-50" x-data="{ editing: false, qty: {{ $bike->stock_quantity }} }">
                        <td class="px-4 py-3 flex items-center gap-3">
                            @php $img = $bike->images->first(); @endphp
                            <a href="{{ route('motorcycles.show', $bike) }}" target="_blank" class="flex-shrink-0">
                                @if($img)
                                    <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-12 h-10 rounded object-cover hover:opacity-80 transition-opacity">
                                @else
                                    <div class="w-12 h-10 rounded bg-gray-100"></div>
                                @endif
                            </a>
                            <div>
                                <a href="{{ route('motorcycles.show', $bike) }}" target="_blank"
                                   class="font-medium text-gray-900 hover:text-indigo-600">{{ $bike->year }} {{ $bike->make }} {{ $bike->model }}</a>
                                @if($bike->color)<p class="text-xs text-gray-400">{{ $bike->color }}</p>@endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">£{{ number_format($bike->price, 2) }}</td>
                        <td class="px-4 py-3">
                            <div x-show="!editing" class="flex items-center gap-2">
                                <span class="font-medium {{ $bike->stock_quantity > 0 ? 'text-green-700' : 'text-red-600' }}">
                                    {{ $bike->stock_quantity }}
                                </span>
                                <button @click="editing = true" class="text-xs text-indigo-500 hover:text-indigo-700">edit</button>
                            </div>
                            <form x-show="editing" method="POST" action="{{ route('admin.motorcycles.stock', $bike) }}" class="flex items-center gap-1">
                                @csrf @method('PATCH')
                                <input type="number" name="stock_quantity" x-model="qty" min="0"
                                       class="w-20 rounded border-gray-300 text-sm px-2 py-1">
                                <button type="submit" class="text-xs bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700">Save</button>
                                <button type="button" @click="editing = false" class="text-xs text-gray-400 hover:text-gray-600">Cancel</button>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            @if($bike->is_featured)
                                <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded">Featured</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('motorcycles.show', $bike) }}" target="_blank" class="text-xs text-gray-400 hover:text-gray-600 mr-2">View</a>
                            <a href="{{ route('admin.motorcycles.edit', $bike) }}" class="text-xs text-indigo-600 hover:text-indigo-800 mr-2">Edit</a>
                            <form method="POST" action="{{ route('admin.motorcycles.destroy', $bike) }}" class="inline"
                                  onsubmit="return confirm('Delete this motorcycle?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            No motorcycles yet. <a href="{{ route('admin.motorcycles.create') }}" class="text-indigo-600 hover:underline">Add the first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $motorcycles->links() }}</div>

</x-admin-layout>
