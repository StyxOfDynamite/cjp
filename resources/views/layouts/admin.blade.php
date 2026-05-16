<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | {{ config('seo.business_name', config('app.name')) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-gray-200 flex-shrink-0 flex flex-col">
        <div class="px-6 py-5 border-b border-gray-700">
            <a href="{{ route('motorcycles.index') }}" class="text-white font-bold text-lg">
                {{ config('seo.business_name', config('app.name')) }}
            </a>
            <p class="text-xs text-gray-400 mt-1">Admin Panel</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('admin.motorcycles.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.motorcycles.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800' }}">
                Motorcycles
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-gray-700 text-xs text-gray-400">
            <p class="mb-2">{{ auth()->user()?->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:text-white">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">{{ $heading ?? 'Admin' }}</h1>
            <a href="{{ route('motorcycles.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800" target="_blank">
                View public site &rarr;
            </a>
        </header>

        <main class="flex-1 overflow-auto p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded text-sm">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
