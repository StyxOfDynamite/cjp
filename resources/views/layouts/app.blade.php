<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

<nav class="glass-blur border-b border-white/10 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-18" style="height:4.5rem">
        <a href="{{ route('home') }}" class="text-2xl font-extrabold tracking-tight text-white uppercase">
            {{ config('seo.business_name', config('app.name')) }}
        </a>
        <div class="hidden sm:flex items-center gap-8 text-sm font-semibold uppercase tracking-wide">
            <a href="{{ route('motorcycles.index') }}" class="{{ request()->routeIs('motorcycles.*') ? 'text-amber-400' : 'text-zinc-300 hover:text-white' }} transition-colors">Motorcycles</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-amber-400' : 'text-zinc-300 hover:text-white' }} transition-colors">About</a>
            <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'text-amber-400' : 'text-zinc-300 hover:text-white' }} transition-colors">Contact</a>
        </div>
        <div class="flex items-center gap-4 text-sm font-semibold uppercase tracking-wide">
            @auth
                <a href="{{ route('admin.motorcycles.index') }}" class="text-amber-400 hover:text-amber-300 transition-colors">Admin</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-zinc-400 hover:text-zinc-200 transition-colors">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-zinc-300 hover:text-white transition-colors">Staff Login</a>
            @endauth
        </div>
    </div>
</nav>

<main>
    {{ $slot }}
</main>

<footer class="bg-zinc-950 border-t border-white/10 text-zinc-300 mt-16">
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
        <div>
            <h3 class="text-white font-semibold mb-2 tracking-tight">Quick Links</h3>
            <ul class="text-sm space-y-1">
                <li><a href="{{ route('motorcycles.index') }}" class="hover:text-white transition-colors">Browse Motorcycles</a></li>
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
                <li><a href="{{ route('sitemap') }}" class="hover:text-white transition-colors">Sitemap</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/5 text-center text-xs text-zinc-600 py-4">
        &copy; {{ date('Y') }} {{ config('seo.business_name') }}. All rights reserved.
    </div>
</footer>

@stack('scripts')
</body>
</html>
