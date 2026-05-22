<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | {{ config('seo.business_name', config('app.name')) }}</title>
@include('partials.favicon')
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

            @php $unread = \App\Models\Enquiry::unread()->count(); @endphp
            <a href="{{ route('admin.enquiries.index') }}"
               @click="open = false"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('admin.enquiries.*') ? 'bg-white/10 text-amber-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Enquiries
                @if($unread > 0)
                    <span class="ml-auto bg-amber-500 text-black text-xs font-bold px-1.5 py-0.5 rounded-full leading-none">{{ $unread }}</span>
                @endif
            </a>

            <div class="pt-4 pb-1">
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-zinc-600">Content</p>
            </div>
            <a href="{{ route('admin.about.edit') }}"
               @click="open = false"
               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium transition-colors {{ request()->routeIs('admin.about.edit') ? 'bg-white/10 text-amber-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                About Page
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
