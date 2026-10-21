<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Campus Find') }} | Lost and found for USTP</title>
    <meta name="description" content="Report, search, and claim lost items on campus. Campus Find keeps every lost and found report in one place.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700|figtree:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-display { font-family: 'Bricolage Grotesque', 'Figtree', ui-sans-serif, system-ui, sans-serif; }
        .font-body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>

{{--
    Campus Find front page (design/cf-101-add-frontpage-ui)
    Palette: ink #0F2A4A, tag #F5B301, mist #EEF3F9, slate #475569
    TODO (logic branch): point the search form and category links at the real items route.
--}}
<body class="font-body bg-white text-slate-700 antialiased">

    {{-- Top navigation --}}
    <header class="bg-[#0F2A4A]">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8" aria-label="Main">
            <a href="{{ url('/') }}" class="flex items-center gap-2 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                </span>
                <span class="font-display text-xl font-bold text-white">Campus Find</span>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="rounded-lg bg-[#F5B301] px-4 py-2 text-sm font-semibold text-[#0F2A4A] hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Go to dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-lg bg-[#F5B301] px-4 py-2 text-sm font-semibold text-[#0F2A4A] hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </header>

    <main>
        {{-- Hero --}}
        <section class="bg-[#0F2A4A] pb-16 pt-8 sm:pb-24 sm:pt-14">
            <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-[1.15fr_0.85fr] lg:gap-16 lg:px-8">

                <div>
                    <h1 class="font-display text-4xl font-bold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                        Lost something on campus? Start by searching.
                    </h1>
                    <p class="mt-5 max-w-xl text-lg leading-relaxed text-slate-300">
                        Campus Find keeps lost and found reports in one place, so you no longer have to scroll through social media posts. Found items are kept safe at the Lost and Found office until the real owner claims them.
                    </p>

                    {{-- Search bar --}}
                    <form action="{{ url('/items') }}" method="GET" role="search" class="mt-8 max-w-xl">
                        <label for="hero-search" class="sr-only">Search lost and found items</label>
                        <div class="flex flex-col gap-2 rounded-2xl bg-white p-2 shadow-xl sm:flex-row">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                                <input id="hero-search" name="q" type="search" placeholder="Item name, like black umbrella or ID"
                                       class="w-full rounded-xl border-0 py-3.5 pl-12 pr-4 text-base text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0F2A4A]">
                            </div>
                            <button type="submit" class="rounded-xl bg-[#F5B301] px-6 py-3.5 text-base font-semibold text-[#0F2A4A] hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A] focus-visible:ring-offset-2">
                                Search items
                            </button>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                            <span class="text-slate-400">Browse by category:</span>
                            @foreach (['Bags', 'Electronics', 'IDs and wallets', 'Clothing', 'Keys', 'Books'] as $category)
                                <a href="{{ url('/items') }}?category={{ urlencode($category) }}"
                                   class="rounded-full border border-white/20 px-3 py-1 text-slate-200 hover:border-[#F5B301] hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
                                    {{ $category }}
                                </a>
                            @endforeach
                        </div>
                    </form>

                    {{-- Report actions --}}
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="rounded-lg border border-white/30 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">I lost an item</a>
                        <a href="{{ route('register') }}" class="rounded-lg border border-white/30 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">I found an item</a>
                    </div>
                </div>

                {{-- Item tag: shows the real status path an item goes through --}}
                <div class="mx-auto w-full max-w-sm lg:max-w-none">
                    <div class="relative rotate-2 rounded-3xl bg-[#F5B301] p-1.5 shadow-2xl">
                        <div class="rounded-[1.4rem] bg-white p-6 pt-10">
                            <span class="absolute left-1/2 top-5 h-4 w-4 -translate-x-1/2 rounded-full bg-[#0F2A4A]" aria-hidden="true"></span>

                            <p class="text-sm text-slate-500">Found item</p>
                            <p class="font-display text-2xl font-bold text-[#0F2A4A]">Black umbrella</p>
                            <p class="mt-1 text-sm text-slate-500">Found near the library, kept at the Lost and Found office</p>

                            <ol class="mt-6 space-y-4" aria-label="Example item status">
                                <li class="flex items-center gap-3 text-sm text-slate-400">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#EEF3F9] text-[#0F2A4A]" aria-hidden="true">&#10003;</span>
                                    Reported by finder
                                </li>
                                <li class="flex items-center gap-3 text-sm font-semibold text-[#0F2A4A]">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">&#9679;</span>
                                    In custody
                                    <span class="ml-auto rounded-full bg-[#EEF3F9] px-2.5 py-0.5 text-xs font-medium">Current status</span>
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-500">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-200" aria-hidden="true"></span>
                                    Reserved, awaiting pickup
                                </li>
                                <li class="flex items-center gap-3 text-sm text-slate-500">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-200" aria-hidden="true"></span>
                                    Claimed
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- How it works: a real sequence, so numbering is meaningful --}}
        <section class="bg-[#EEF3F9] py-16 sm:py-20" aria-labelledby="how-heading">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <h2 id="how-heading" class="font-display text-3xl font-bold text-[#0F2A4A] sm:text-4xl">How getting an item back works</h2>
                <p class="mt-3 max-w-2xl text-lg text-slate-600">Every item is checked by an administrator before it is released, so it only goes to its real owner.</p>

                <ol class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['Report it', 'Post what you lost or found, with the name, description, location, and date. A photo is optional.'],
                        ['Drop it off', 'Finders bring the item to the Lost and Found office, where the administrator records it as in custody.'],
                        ['Claim it', 'Find your item in the list and send a claim with proof that it is yours.'],
                        ['Pick it up', 'Once approved, show your school ID at the office. The administrator checks it and hands over the item.'],
                    ] as $i => [$title, $text])
                        <li class="rounded-2xl bg-white p-6">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0F2A4A] font-display text-base font-bold text-white">{{ $i + 1 }}</span>
                            <h3 class="mt-4 font-display text-xl font-bold text-[#0F2A4A]">{{ $title }}</h3>
                            <p class="mt-2 leading-relaxed text-slate-600">{{ $text }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- Closing call to action --}}
        <section class="bg-white py-16 sm:py-20">
            <div class="mx-auto flex max-w-6xl flex-col items-start justify-between gap-6 px-4 sm:px-6 md:flex-row md:items-center lg:px-8">
                <div>
                    <h2 class="font-display text-3xl font-bold text-[#0F2A4A]">Ready to report or claim an item?</h2>
                    <p class="mt-2 text-lg text-slate-600">Create an account with your school email. It only takes a minute.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('register') }}" class="rounded-lg bg-[#0F2A4A] px-6 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">Create account</a>
                    <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 px-6 py-3 font-semibold text-[#0F2A4A] hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A] focus-visible:ring-offset-2">Log in</a>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-[#0F2A4A] text-slate-300">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <p class="font-display text-xl font-bold text-white">Campus Find</p>
                <p class="mt-2 max-w-xs text-sm leading-relaxed">A lost and found system for the University of Science and Technology of Southern Philippines.</p>
            </div>
            <nav aria-label="Footer">
                <p class="font-semibold text-white">Get started</p>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="hover:text-white focus:outline-none focus-visible:underline">Log in</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white focus:outline-none focus-visible:underline">Register</a></li>
                    <li><a href="{{ url('/items') }}" class="hover:text-white focus:outline-none focus-visible:underline">Search items</a></li>
                </ul>
            </nav>
            <div>
                <p class="font-semibold text-white">About this project</p>
                <p class="mt-3 text-sm leading-relaxed">College of Information Technology and Computing<br>Software Engineering (IT3R2), Group 2</p>
            </div>
        </div>
        <div class="border-t border-white/10">
            <p class="mx-auto max-w-6xl px-4 py-5 text-sm text-slate-400 sm:px-6 lg:px-8">&copy; {{ date('Y') }} Campus Find. School project, not an official USTP service.</p>
        </div>
    </footer>
</body>
</html>