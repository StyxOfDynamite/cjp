<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | {{ config('seo.business_name', config('app.name')) }}</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" x-data="{ open: false }" @keydown.escape.window="open = false">

<div class="min-h-screen flex bg-zinc-900">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/60 z-20"
         style="display:none">
    </div>

    {{-- Sidebar --}}
    <aside
        :class="open ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-30 w-72 bg-zinc-950 border-r border-white/10 flex flex-col transition-transform duration-200"
    >
        <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
            <div>
                <a href="{{ route('home') }}" class="text-white font-bold text-lg tracking-tight">
                    {{ config('seo.business_name', config('app.name')) }}
                </a>
                <p class="text-xs text-zinc-500 mt-0.5 uppercase tracking-widest">Admin</p>
            </div>
            {{-- Close button (mobile only) --}}
            <button @click="open = false" class="lg:hidden text-zinc-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('admin.motorcycles.index') }}"
               @click="open = false"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('admin.motorcycles.index') || request()->routeIs('admin.motorcycles.edit') ? 'bg-white/10 text-amber-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                All Motorcycles
            </a>
            <a href="{{ route('admin.motorcycles.create') }}"
               @click="open = false"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('admin.motorcycles.create') ? 'bg-white/10 text-amber-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/>
                </svg>
                Add New
            </a>
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <p class="text-xs text-zinc-300 font-medium mb-1">{{ auth()->user()?->name }}</p>
            <p class="text-xs text-zinc-600 mb-3">{{ auth()->user()?->email }}</p>
            <div class="flex items-center gap-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-zinc-500 hover:text-white transition-colors">Logout</button>
                </form>
                <a href="{{ route('home') }}" target="_blank" class="text-xs text-amber-400 hover:text-amber-300 transition-colors">
                    Public site &rarr;
                </a>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
        <header class="glass border-b border-white/10 px-4 sm:px-6 py-4 flex items-center gap-4">
            {{-- Hamburger --}}
            <button @click="open = true" class="text-zinc-400 hover:text-white transition-colors flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-base font-semibold text-white tracking-tight flex-1 truncate">{{ $heading ?? 'Admin' }}</h1>
            <a href="{{ route('admin.motorcycles.create') }}"
               class="bg-amber-500 text-black text-xs font-semibold px-4 py-2 hover:bg-amber-400 transition-colors whitespace-nowrap uppercase tracking-wide">
                + Add New
            </a>
        </header>

        <main class="flex-1 overflow-auto p-4 sm:p-6">
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
