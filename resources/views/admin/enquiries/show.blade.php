<x-admin-layout heading="Enquiry">

    <div class="max-w-2xl space-y-6">

        <div class="glass p-6 space-y-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-white">{{ $enquiry->name }}</h2>
                    <div class="mt-1 flex items-center gap-3 text-sm text-zinc-400">
                        <a href="mailto:{{ $enquiry->email }}" class="hover:text-amber-400 transition-colors">{{ $enquiry->email }}</a>
                        @if($enquiry->phone)
                            <span class="text-zinc-600">&middot;</span>
                            <a href="tel:{{ $enquiry->phone }}" class="hover:text-amber-400 transition-colors">{{ $enquiry->phone }}</a>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-zinc-600">{{ $enquiry->created_at->format('j M Y, g:ia') }}</p>
                </div>

                @if($enquiry->isRead())
                    <form method="POST" action="{{ route('admin.enquiries.unread', $enquiry) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs text-zinc-500 hover:text-zinc-300 border border-white/10 px-3 py-1.5 transition-colors">
                            Mark unread
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.enquiries.read', $enquiry) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs text-zinc-500 hover:text-zinc-300 border border-white/10 px-3 py-1.5 transition-colors">
                            Mark read
                        </button>
                    </form>
                @endif
            </div>

            <div class="border-t border-white/10 pt-5">
                <p class="text-sm text-zinc-300 whitespace-pre-wrap">{{ $enquiry->message }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('admin.enquiries.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">&larr; All enquiries</a>
            <a href="mailto:{{ $enquiry->email }}" class="bg-amber-500 text-black text-sm font-semibold px-5 py-2 hover:bg-amber-400 transition-colors">
                Reply by email
            </a>
        </div>

    </div>

</x-admin-layout>
