<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campus Find') }}</title>

        <!-- Fonts (same as the front page) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700|figtree:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; }
            .font-body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>

    {{--
        Campus Find user layout (design/cf-002/user-dashboard-ui)
        Sidebar on the left, page content on the right.
        Palette: ink #0F2A4A, tag #F5B301, mist #EEF3F9, slate #475569
    --}}
    <body class="font-body bg-slate-50 text-slate-700 antialiased">
        <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="min-h-screen">

            @include('layouts.sidebar')

            <div class="lg:pl-64">
                {{-- Top bar, only on phones and tablets --}}
                <div class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur lg:hidden">
                    <button type="button" @click="sidebarOpen = true"
                            class="-ml-1 rounded-lg p-2 text-[#0F2A4A] hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]"
                            aria-label="Open menu">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        </span>
                        <span class="font-display text-lg font-bold text-[#0F2A4A]">Campus Find</span>
                    </a>
                </div>

                {{-- Page heading (used by Profile and other pages) --}}
                @isset($header)
                    <header class="border-b border-slate-200 bg-white">
                        <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>