<x-admin-layout heading="About Page">

    <div class="max-w-3xl">

        <p class="text-sm text-zinc-500 mb-6">
            Edit the story section of the public about page. Changes are reflected immediately after saving.
            <a href="{{ route('about') }}" target="_blank" class="text-amber-400 hover:text-amber-300 transition-colors ml-1">View public page &rarr;</a>
        </p>

        <form method="POST" action="{{ route('admin.about.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Tagline --}}
            <div class="glass p-5">
                <label for="tagline" class="block text-xs font-semibold uppercase tracking-widest text-zinc-400 mb-3">
                    Tagline
                </label>
                <input
                    type="text"
                    id="tagline"
                    name="tagline"
                    value="{{ old('tagline', $content['tagline'] ?? '') }}"
                    placeholder="e.g. 30 years in the trade."
                    class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500"
                >
                @error('tagline')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-zinc-600">Displayed as the large bold heading above the body text.</p>
            </div>

            {{-- Body --}}
            <div class="glass p-5">
                <label for="body" class="block text-xs font-semibold uppercase tracking-widest text-zinc-400 mb-3">
                    Body Text
                </label>
                <textarea
                    id="body"
                    name="body"
                    rows="12"
                    placeholder="Write your about page content here. Use blank lines to separate paragraphs."
                    class="w-full bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-600 text-sm px-3 py-2 rounded-none focus:ring-amber-500 focus:border-amber-500 resize-y font-mono leading-relaxed"
                >{{ old('body', $content['body'] ?? '') }}</textarea>
                @error('body')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-zinc-600">Each blank line becomes a new paragraph on the public page.</p>
            </div>

            {{-- Preview --}}
            @if(!empty($content['body']))
            <div class="glass p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-zinc-400 mb-3">Preview</p>
                <div class="text-zinc-300 text-sm leading-relaxed space-y-3">
                    @foreach(array_filter(explode("\n\n", $content['body'] ?? '')) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex items-center gap-4">
                <button type="submit"
                        class="bg-amber-500 text-black text-sm font-semibold px-6 py-2.5 hover:bg-amber-400 transition-colors uppercase tracking-wide">
                    Save Changes
                </button>
                <a href="{{ route('about') }}" target="_blank"
                   class="text-sm text-zinc-500 hover:text-zinc-300 transition-colors">
                    View public page &rarr;
                </a>
            </div>

        </form>

    </div>

</x-admin-layout>
