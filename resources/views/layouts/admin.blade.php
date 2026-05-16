<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | {{ config('seo.business_name', config('app.name')) }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="w-64 bg-zinc-950 border-r border-white/10 flex-shrink-0 flex flex-col">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ route('motorcycles.index') }}" class="text-white font-bold text-lg tracking-tight">
                {{ config('seo.business_name', config('app.name')) }}
            </a>
            <p class="text-xs text-zinc-500 mt-1 uppercase tracking-widest">Admin Panel</p>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('admin.motorcycles.index') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('admin.motorcycles.*') ? 'bg-white/10 text-amber-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                Motorcycles
            </a>
        </nav>
        <div class="px-4 py-4 border-t border-white/10 text-xs text-zinc-500">
            <p class="mb-2 text-zinc-300">{{ auth()->user()?->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="hover:text-white transition-colors">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-zinc-900">
        <header class="glass border-b border-white/10 px-6 py-4 flex items-center justify-between">
            <h1 class="text-base font-semibold text-white tracking-tight">{{ $heading ?? 'Admin' }}</h1>
            <a href="{{ route('motorcycles.index') }}" class="text-sm text-amber-400 hover:text-amber-300 transition-colors" target="_blank">
                View public site &rarr;
            </a>
        </header>

        <main class="flex-1 overflow-auto p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
