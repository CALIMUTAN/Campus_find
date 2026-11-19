<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Campus Find') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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
                    {{-- fix/cf-017: floating "new notifications" toast, stays put while scrolling --}}
                    @auth
                        @php
                            $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                        @endphp

                        @if ($unreadNotificationCount > 0 && ! request()->routeIs('notifications.index'))
                            <div
                                x-data="{ dismissed: false }"
                                x-show="!dismissed"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-3"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-x-4 top-20 z-50 lg:inset-x-auto lg:right-6 lg:top-6 lg:w-full lg:max-w-sm"
                            >
                                <div class="flex items-start gap-3 rounded-2xl bg-white p-4 shadow-xl ring-1 ring-slate-900/5">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#FFF8E1] text-[#946200]" aria-hidden="true">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0 flex-1 pt-0.5">
                                        <p class="font-display text-sm font-bold text-[#0F2A4A]">
                                            {{ $unreadNotificationCount }} new {{ \Illuminate\Support\Str::plural('notification', $unreadNotificationCount) }}
                                        </p>
                                        <p class="mt-0.5 text-sm text-slate-500">
                                            @if ($unreadNotificationCount === 1)
                                                Something changed on one of your reports or claims.
                                            @else
                                                Something changed on {{ $unreadNotificationCount }} of your reports or claims.
                                            @endif
                                        </p>
                                        <a href="{{ route('notifications.index') }}" class="mt-2 inline-block text-sm font-semibold text-[#0F2A4A] underline decoration-[#F5B301] decoration-2 underline-offset-4 hover:decoration-[#0F2A4A]">
                                            View notifications
                                        </a>
                                    </div>

                                    <button
                                        type="button"
                                        @click="dismissed = true"
                                        class="shrink-0 rounded p-1 text-slate-400 hover:text-slate-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]"
                                        aria-label="Dismiss">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endauth

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>