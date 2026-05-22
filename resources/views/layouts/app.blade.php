<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@include('partials.favicon')
@include('partials.seo')
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/public.js'])
    @if(config('services.google.analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google.analytics_id') }}');
    </script>
    @endif
</head>
<body class="font-sans antialiased">

{{-- Skip to main content (accessibility + crawlers) --}}
<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:bg-amber-500 focus:text-black focus:px-4 focus:py-2 focus:text-sm focus:font-semibold">
    Skip to main content
</a>

<nav aria-label="Main navigation" class="bg-white border-b border-zinc-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between" style="height:5rem">
        <a href="{{ route('home') }}" aria-label="{{ config('seo.business_name') }} — Home">
            <img src="{{ config('seo.logo_url') }}" alt="{{ config('seo.business_name') }}" class="h-14 w-auto">
        </a>
        <ul class="flex items-center gap-5 sm:gap-8 text-sm font-semibold uppercase tracking-wide list-none" role="list">
            <li>
                <a href="{{ route('motorcycles.index') }}"
                   class="{{ request()->routeIs('motorcycles.*') ? 'text-amber-500' : 'text-zinc-600 hover:text-zinc-900' }} transition-colors"
                   @if(request()->routeIs('motorcycles.*')) aria-current="page" @endif>
                    Browse
                </a>
            </li>
            <li>
                <a href="{{ route('about') }}"
                   class="{{ request()->routeIs('about') ? 'text-amber-500' : 'text-zinc-600 hover:text-zinc-900' }} transition-colors"
                   @if(request()->routeIs('about')) aria-current="page" @endif>
                    About
                </a>
            </li>
            <li>
                <a href="{{ route('contact') }}"
                   class="{{ request()->routeIs('contact') ? 'text-amber-500' : 'text-zinc-600 hover:text-zinc-900' }} transition-colors"
                   @if(request()->routeIs('contact')) aria-current="page" @endif>
                    Contact
                </a>
            </li>
        </ul>
        @auth
        <div class="flex items-center gap-4 text-sm font-semibold uppercase tracking-wide">
            <a href="{{ route('admin.motorcycles.index') }}" class="text-amber-500 hover:text-amber-600 transition-colors">Admin</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-zinc-500 hover:text-zinc-800 transition-colors">Logout</button>
            </form>
        </div>
        @endauth
    </div>
</nav>

<main id="main-content">
    {{ $slot }}
</main>

<footer aria-label="Site footer" class="bg-zinc-950 border-t border-white/10 text-zinc-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
            <h3 class="text-white font-semibold mb-2 tracking-tight">{{ config('seo.business_name') }}</h3>
            <address class="not-italic text-sm leading-6">
                {{ config('seo.address_street') }}<br>
                {{ config('seo.address_city') }}, {{ config('seo.address_region') }} {{ config('seo.address_postcode') }}<br>
                <a href="tel:{{ config('seo.phone') }}" class="text-amber-400 hover:text-amber-300 transition-colors">{{ config('seo.phone') }}</a>
            </address>
        </div>
        <div>
            <h3 class="text-white font-semibold mb-2 tracking-tight">Hours</h3>
            <ul class="text-sm space-y-1">
                @foreach(config('seo.opening_hours_display', []) as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
        <nav aria-label="Footer navigation">
            <h3 class="text-white font-semibold mb-2 tracking-tight">Quick Links</h3>
            <ul class="text-sm space-y-1">
                <li><a href="{{ route('motorcycles.index') }}" class="hover:text-white transition-colors">Browse Motorcycles</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                <li><a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap</a></li>
                @guest
                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff Login</a></li>
                @endguest
            </ul>
        </nav>
    </div>
    <div class="border-t border-white/5 text-center text-xs text-zinc-600 py-4">
        &copy; {{ date('Y') }} {{ config('seo.business_name') }}. All rights reserved.
    </div>
</footer>

@stack('scripts')
</body>
</html>
