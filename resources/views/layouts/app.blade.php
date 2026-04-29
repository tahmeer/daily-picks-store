<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700|instrument-sans:400,500,600&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-app-mesh antialiased">
    <div class="min-h-full" x-data="{ sidebarOpen: false }">
        <header class="no-print sticky top-0 z-30 border-b border-emerald-900/20 bg-gradient-to-r from-emerald-900 to-teal-900 lg:hidden">
            <div class="flex items-center justify-between px-4 py-3.5">
                <button type="button" class="rounded-xl p-2 text-emerald-100 hover:bg-white/10" @click="sidebarOpen = true" aria-label="Open menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                        <svg class="h-4 w-4 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    <span class="font-display font-semibold text-white">{{ config('app.name') }}</span>
                </div>
                <span class="w-10"></span>
            </div>
        </header>

        <div class="no-print fixed inset-0 z-40 bg-emerald-950/60 backdrop-blur-sm lg:hidden" x-show="sidebarOpen" x-transition.opacity x-cloak @click="sidebarOpen = false"></div>
        <aside
            class="no-print fixed inset-y-0 left-0 z-50 w-[17rem] -translate-x-full transform bg-gradient-to-b from-emerald-950 via-emerald-900 to-slate-950 shadow-2xl shadow-emerald-950/50 ring-1 ring-white/10 transition-transform duration-300 lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-400/15 via-transparent to-transparent"></div>
            <div class="relative flex h-full flex-col">
                <div class="border-b border-white/10 px-5 pb-7 pt-8">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 shadow-lg shadow-emerald-900/40 ring-2 ring-white/20">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9zm8 8h.01"/></svg>
                        </span>
                        <div>
                            <p class="font-display text-lg font-bold leading-tight text-white">{{ config('app.name') }}</p>
                            <p class="mt-1 text-xs font-medium text-emerald-200/90">Inventory &amp; POS</p>
                        </div>
                    </div>
                </div>
                <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto px-3 py-5">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span>Dashboard</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('items.index') }}" :active="request()->routeIs('items.*')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Inventory</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('restock-logs.index') }}" :active="request()->routeIs('restock-logs.*')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Restock logs</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('sales.create') }}" :active="request()->routeIs('sales.create')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>New Sale</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index') || request()->routeIs('sales.receipt')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Sales History</span>
                    </x-nav-link>
                    <x-nav-link href="{{ route('analytics.index') }}" :active="request()->routeIs('analytics.*')">
                        <svg class="h-5 w-5 shrink-0 opacity-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                        <span>Analytics</span>
                    </x-nav-link>
                </nav>
                <div class="border-t border-white/10 p-4">
                    <button type="button" class="w-full rounded-xl border border-white/15 bg-white/5 px-3 py-2.5 text-sm font-medium text-emerald-100/90 transition hover:bg-white/10 lg:hidden" @click="sidebarOpen = false">Close menu</button>
                    <p class="mt-3 hidden text-center text-[11px] leading-relaxed text-emerald-400/70 lg:block">Pakistani Rupees (PKR)</p>
                </div>
            </div>
        </aside>

        <div class="lg:pl-[17rem]">
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
                @if (session('success'))
                    <div class="no-print mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200/80 bg-gradient-to-r from-emerald-50 to-teal-50 px-5 py-4 text-sm font-medium text-emerald-950 shadow-lg shadow-emerald-900/5 ring-1 ring-emerald-100" role="alert">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-white shadow-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span class="pt-0.5">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="no-print mb-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-950 shadow-md ring-1 ring-red-100" role="alert">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-500 text-white shadow-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </span>
                        <span class="pt-0.5">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="no-print mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-950 shadow-md ring-1 ring-red-100" role="alert">
                        <ul class="list-inside list-disc space-y-1.5 font-medium">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <style>[x-cloak]{display:none!important}</style>
    @stack('scripts')
</body>
</html>
