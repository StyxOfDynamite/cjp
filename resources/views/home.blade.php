<x-app-layout>
    @php
        $title = config('seo.business_name') . ' — Motorcycles For Sale';
        $description = config('seo.default_description');
        $canonical = route('home');
    @endphp

    @if($bikes->isNotEmpty())
        @push('head')
            <link rel="preload" as="image" href="{{ $bikes->first()->images->first()?->url }}">
        @endpush
    @endif

    {{-- Carousel --}}
    <div
        x-data="{
            current: 0,
            total: {{ $bikes->count() }},
            timer: null,
            start() {
                this.timer = setInterval(() => this.next(), 5000);
            },
            stop() {
                clearInterval(this.timer);
            },
            next() { this.current = (this.current + 1) % this.total },
            prev() { this.current = (this.current - 1 + this.total) % this.total },
            go(i) { this.current = i; this.stop(); this.start(); },
        }"
        x-init="start()"
        class="relative overflow-hidden"
        style="height: 80vh; min-height: 520px;"
    >
        {{-- Slides --}}
        @foreach($bikes as $i => $bike)
            @php $img = $bike->images->first(); @endphp
            <div
                x-show="current === {{ $i }}"
                x-transition:enter="transition-opacity duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-700"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0"
                style="display:none"
            >
                {{-- Background image --}}
                @if($img)
                    <img src="{{ $img->url }}"
                         alt="{{ $bike->year }} {{ $bike->make }} {{ $bike->model }}"
                         loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                         fetchpriority="{{ $i === 0 ? 'high' : 'auto' }}"
                         class="absolute inset-0 w-full h-full object-cover">
                @endif
                {{-- Gradient overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/60 to-transparent"></div>

                {{-- Content --}}
                <div class="absolute inset-0 flex items-end">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 w-full">
                        @if($bike->is_featured)
                            <span class="inline-block text-xs font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5 mb-4 uppercase tracking-widest">Featured</span>
                        @endif
                        <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-none mb-3">
                            {{ $bike->year }} {{ $bike->make }}<br>{{ $bike->model }}
                        </h2>
                        <div class="flex items-center gap-4 mb-6 text-zinc-300 text-sm">
                            @if($bike->engine_size_cc)<span>{{ $bike->engine_size_cc }}cc</span>@endif
                            @if($bike->mileage_miles)<span>&middot;</span><span>{{ number_format($bike->mileage_miles) }} mi</span>@endif
                            @if($bike->color)<span>&middot;</span><span>{{ $bike->color }}</span>@endif
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-3xl font-bold text-amber-400">£{{ number_format($bike->price, 2) }}</span>
                            <a href="{{ route('motorcycles.show', $bike) }}"
                               class="bg-amber-500 text-black font-semibold text-sm px-6 py-3 hover:bg-amber-400 transition-colors uppercase tracking-wide">
                                View Bike
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Prev / Next --}}
        @if($bikes->count() > 1)
            <button @click="prev(); stop(); start()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 p-3 glass-blur text-white/70 hover:text-white transition-colors z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next(); stop(); start()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 p-3 glass-blur text-white/70 hover:text-white transition-colors z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Dot navigation --}}
            <div class="absolute bottom-6 right-6 flex items-center gap-2 z-10">
                @foreach($bikes as $i => $bike)
                    <button @click="go({{ $i }})"
                            :class="current === {{ $i }} ? 'bg-amber-400 w-6' : 'bg-white/30 hover:bg-white/60 w-2'"
                            class="h-2 transition-all duration-300">
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Slide counter --}}
        <div class="absolute top-6 right-6 z-10 text-xs font-medium text-white/50 tabular-nums">
            <span x-text="current + 1"></span> / {{ $bikes->count() }}
        </div>
    </div>

    {{-- Browse CTA strip --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <a href="{{ route('motorcycles.index', ['in_stock' => 1]) }}"
                   class="text-white font-bold text-xl tracking-tight hover:text-amber-400 transition-colors">
                    {{ $inStockCount }} bikes currently in stock
                </a>
                <p class="text-zinc-400 text-sm mt-1">New stock added regularly — check back often.</p>
            </div>
            <a href="{{ route('motorcycles.index', ['in_stock' => 1]) }}"
               class="bg-amber-500 text-black font-semibold px-8 py-3 hover:bg-amber-400 transition-colors uppercase tracking-wide text-sm whitespace-nowrap">
                View In-Stock Bikes
            </a>
        </div>
    </div>

    {{-- About teaser --}}
    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-xs font-medium uppercase tracking-widest text-amber-400 mb-4">About Us</p>
                <h2 class="text-3xl font-bold tracking-tight text-white mb-4">30 years in the trade.</h2>
                <p class="text-zinc-300 leading-relaxed">
                    Based in {{ config('seo.address_city') }}, we specialise in high quality, low mileage motorcycles and scooters.
                    With 30 years of trade contacts, if we don't have what you're looking for we can usually find it.
                </p>
                <div class="mt-6 flex items-center gap-6">
                    <a href="{{ route('about') }}" class="text-amber-400 hover:text-amber-300 text-sm font-semibold uppercase tracking-wide transition-colors">
                        Learn more &rarr;
                    </a>
                    <a href="{{ route('contact') }}" class="text-zinc-400 hover:text-white text-sm font-semibold uppercase tracking-wide transition-colors">
                        Contact us
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="glass p-6">
                    <p class="text-4xl font-extrabold text-amber-400 tracking-tight">30+</p>
                    <p class="text-zinc-400 text-sm mt-1">Years experience</p>
                </div>
                <div class="glass p-6">
                    <p class="text-4xl font-extrabold text-amber-400 tracking-tight">{{ $inStockCount }}</p>
                    <p class="text-zinc-400 text-sm mt-1">Bikes in stock</p>
                </div>
                <div class="glass p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Call us</p>
                    <a href="tel:{{ config('seo.phone') }}" class="text-white font-semibold text-sm hover:text-amber-400 transition-colors">
                        {{ config('seo.phone') }}
                    </a>
                </div>
                <div class="glass p-6">
                    <p class="text-xs font-medium uppercase tracking-widest text-zinc-500 mb-2">Location</p>
                    <p class="text-white text-sm">{{ config('seo.address_city') }}</p>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
