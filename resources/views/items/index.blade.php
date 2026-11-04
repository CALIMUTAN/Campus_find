{{--
    Item catalog (feat/cf-009/item-catalog)
    Public browse + search page for lost & found reports.
    Requirements: FR-08, FR-09, FR-10

    Logged-in users get the dashboard sidebar shell; guests get the
    public page shell (same header style as the front page) so anyone
    can still search without an account. Both pull the same search
    form + results from items.partials.catalog, so they never drift.
--}}
@auth
    <x-app-layout>
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            @include('items.partials.catalog')
        </div>
    </x-app-layout>
@else
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Browse items | {{ config('app.name', 'Campus Find') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700|figtree:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; }
            .font-body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>

    <body class="font-body bg-slate-50 text-slate-700 antialiased">

        {{-- Top navigation (same as the front page) --}}
        <header class="bg-[#0F2A4A]">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main">
                <a href="{{ url('/') }}" class="flex items-center gap-2 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    </span>
                    <span class="font-display text-xl font-bold text-white">Campus Find</span>
                </a>

                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-[#F5B301] px-4 py-2 text-sm font-semibold text-[#0F2A4A] hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Register</a>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            @include('items.partials.catalog')
        </main>
    </body>
    </html>
@endauth