<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Campus Find') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700|figtree:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; }
        .font-body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>

{{-- Shared layout for login, register, forgot/reset password, verify email --}}
<body class="font-body bg-white text-slate-700 antialiased">
    <div class="grid min-h-screen lg:grid-cols-[0.9fr_1.1fr]">

        {{-- Brand panel (desktop only) --}}
        <aside class="hidden flex-col justify-between bg-[#0F2A4A] p-12 lg:flex">
            <a href="{{ url('/') }}" class="flex w-fit items-center gap-2 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-white">Campus Find</span>
            </a>

            <div>
                <h2 class="font-display text-4xl font-bold leading-tight text-white">Every lost item has a way back to its owner.</h2>
                <ul class="mt-8 space-y-4 text-slate-300">
                    <li class="flex gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#F5B301]" aria-hidden="true"></span>Found items are kept safe at the Lost and Found office.</li>
                    <li class="flex gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#F5B301]" aria-hidden="true"></span>Claims need proof of ownership, reviewed by an administrator.</li>
                    <li class="flex gap-3"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#F5B301]" aria-hidden="true"></span>Your school ID is checked before any item is released.</li>
                </ul>
            </div>

            <p class="text-sm text-slate-400">College of Information Technology and Computing<br>Software Engineering (IT3R2), Group 2</p>
        </aside>

        {{-- Form panel --}}
        <main class="flex items-center justify-center px-4 py-10 sm:px-8">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A] lg:hidden">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#0F2A4A] text-[#F5B301]" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                        </span>
                        <span class="font-display text-lg font-bold text-[#0F2A4A]">Campus Find</span>
                    </a>
                    <a href="{{ url('/') }}" class="ml-auto text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">Back to home</a>
                </div>

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>