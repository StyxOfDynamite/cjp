<x-admin-layout heading="Enquiries">

    @if($enquiries->isEmpty())
        <div class="glass px-6 py-16 text-center text-zinc-600">
            No enquiries yet.
        </div>
    @else
        <div class="glass divide-y divide-white/5">
            @foreach($enquiries as $enquiry)
                <a href="{{ route('admin.enquiries.show', $enquiry) }}"
                   class="flex items-start gap-4 px-5 py-4 hover:bg-white/5 transition-colors {{ $enquiry->isRead() ? 'opacity-60' : '' }}">

                    {{-- Unread dot --}}
                    <div class="mt-1.5 flex-shrink-0 w-2 h-2 rounded-full {{ $enquiry->isRead() ? 'bg-transparent' : 'bg-amber-400' }}"></div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-3">
                            <span class="font-semibold text-sm text-zinc-100 {{ $enquiry->isRead() ? '' : 'text-white' }}">{{ $enquiry->name }}</span>
                            <span class="text-xs text-zinc-500">{{ $enquiry->email }}</span>
                            @if($enquiry->phone)
                                <span class="text-xs text-zinc-600">{{ $enquiry->phone }}</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-sm text-zinc-400 truncate">{{ $enquiry->message }}</p>
                    </div>

                    <span class="flex-shrink-0 text-xs text-zinc-600 whitespace-nowrap">{{ $enquiry->created_at->diffForHumans() }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $enquiries->links() }}</div>
    @endif

</x-admin-layout>
