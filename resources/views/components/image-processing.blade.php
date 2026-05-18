@props(['pollUrl' => null, 'alt' => ''])

<div
    x-data="{ ready: false, url: '' }"
    @if($pollUrl)
    x-init="(function poll() {
        fetch('{{ $pollUrl }}')
            .then(r => r.json())
            .then(d => { if (d.ready) { url = d.url; ready = true; } else { setTimeout(poll, 2000); } });
    })()"
    @endif
    {{ $attributes }}
>
    <template x-if="ready">
        <img :src="url" alt="{{ $alt }}" class="w-full h-full object-cover">
    </template>
    <template x-if="!ready">
        <div class="w-full h-full flex flex-col items-center justify-center gap-2 bg-zinc-900 border border-white/10">
            <svg class="w-5 h-5 text-zinc-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-xs text-zinc-600">Processing</span>
        </div>
    </template>
</div>
