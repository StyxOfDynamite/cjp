<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        @include('partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
    <body class="font-sans antialiased bg-zinc-950 text-zinc-100">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-12">
            <a href="/" class="mb-8">
                <img src="{{ config('seo.logo_url') }}" alt="{{ config('seo.business_name') }}" class="h-14 w-auto">
            </a>

            <div class="w-full max-w-md glass px-8 py-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
