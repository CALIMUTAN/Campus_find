{{--
    User dashboard (design/cf-002/user-dashboard-ui)
    The profile card is the same yellow tag as the front page's example item.
    TODO (logic branch): $pickup and $items below are sample data.
    Replace them with the user's real reports once the items/claims tables exist.
--}}
@php
    $user = Auth::user();

    // Steps an item goes through (same wording as the front page)
    $steps = ['Reported', 'In custody', 'Reserved, awaiting pickup', 'Claimed'];

    // Sample data for the UI only
    $pickup = ['item' => 'Black umbrella', 'step' => 2];

    $items = [
        ['name' => 'Blue tumbler',            'type' => 'lost',  'place' => 'CITC building, 2nd floor', 'date' => now()->subDays(3),  'step' => 0],
        ['name' => 'Student ID with lanyard', 'type' => 'found', 'place' => 'Canteen',                 'date' => now()->subDays(6),  'step' => 1],
        ['name' => 'Black umbrella',          'type' => 'lost',  'place' => 'Library entrance',        'date' => now()->subDays(12), 'step' => 2],
        ['name' => 'Scientific calculator',   'type' => 'found', 'place' => 'Room 305',                'date' => now()->subDays(21), 'step' => 3],
    ];

    $lostCount  = collect($items)->where('type', 'lost')->count();
    $foundCount = collect($items)->where('type', 'found')->count();
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

        {{-- Page title + the same two actions as the front page --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A]">Dashboard</h1>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('reports.create', 'lost') }}" class="rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">I lost an item</a>
                <a href="{{ route('reports.create', 'found') }}" class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-[#0F2A4A] hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A] focus-visible:ring-offset-2">I found an item</a>
            </div>
        </div>

        {{-- Message after sending a report --}}
        @if (session('report_status'))
            <div x-data="{ show: true }" x-show="show" role="status"
                 class="mt-6 flex items-start gap-3 rounded-xl border border-[#F5B301] bg-[#FFF8E1] px-4 py-3 text-sm text-[#0F2A4A]">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#F5B301] text-xs" aria-hidden="true">&#10003;</span>
                <p class="flex-1">{{ session('report_status') }}</p>
                <button type="button" @click="show = false" class="rounded p-0.5 text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]" aria-label="Close message">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
        @endif

        <div class="mt-10 grid grid-cols-1 items-start gap-12 md:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] lg:gap-16">

            {{-- Profile, drawn as a lost-and-found tag --}}
            <section aria-labelledby="profile-name" class="px-2 sm:px-0">
                <div class="relative -rotate-2 rounded-3xl bg-[#F5B301] p-1.5 shadow-xl shadow-[#0F2A4A]/10 motion-reduce:rotate-0">
                    <div class="rounded-[1.4rem] bg-white px-6 pb-6 pt-11">
                        <span class="absolute left-1/2 top-5 h-4 w-4 -translate-x-1/2 rounded-full bg-[#0F2A4A]" aria-hidden="true"></span>

                        <p class="text-sm text-slate-500">Student</p>
                        <h2 id="profile-name" class="font-display text-2xl font-bold leading-tight text-[#0F2A4A]">{{ $user->name }}</h2>
                        <p class="mt-1 break-all text-sm text-slate-500">{{ $user->email }}</p>

                        <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-dashed border-slate-300 pt-5 text-sm">
                            <div>
                                <dt class="text-slate-500">Joined</dt>
                                <dd class="font-semibold text-[#0F2A4A]">{{ optional($user->created_at)->format('M Y') ?? 'Not set' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-500">Email</dt>
                                <dd class="font-semibold {{ $user->email_verified_at ? 'text-[#0F2A4A]' : 'text-amber-700' }}">
                                    {{ $user->email_verified_at ? 'Verified' : 'Not verified' }}
                                </dd>
                            </div>
                        </dl>

                        <a href="{{ route('profile.edit') }}" class="mt-6 inline-block rounded text-sm font-semibold text-[#0F2A4A] underline decoration-[#F5B301] decoration-2 underline-offset-4 hover:decoration-[#0F2A4A] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]">
                            Edit profile
                        </a>
                    </div>
                </div>
            </section>

            {{-- Item waiting for the user --}}
            <section aria-labelledby="pickup-heading" class="md:pt-4">
                <h2 id="pickup-heading" class="font-display text-2xl font-bold text-[#0F2A4A]">Your {{ strtolower($pickup['item']) }} is ready for pickup</h2>
                <p class="mt-2 max-w-prose text-slate-600">
                    Go to the Lost and Found office and bring your school ID. Staff will check it before handing the item over.
                </p>

                <ol class="mt-6 space-y-4" aria-label="Status of {{ $pickup['item'] }}">
                    @foreach ($steps as $i => $step)
                        @if ($i < $pickup['step'])
                            <li class="flex items-center gap-3 text-sm text-slate-400">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#EEF3F9] text-[#0F2A4A]" aria-hidden="true">&#10003;</span>
                                {{ $step }}
                            </li>
                        @elseif ($i === $pickup['step'])
                            <li class="flex items-center gap-3 text-sm font-semibold text-[#0F2A4A]" aria-current="step">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">&#9679;</span>
                                {{ $step }}
                            </li>
                        @else
                            <li class="flex items-center gap-3 text-sm text-slate-500">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-200" aria-hidden="true"></span>
                                {{ $step }}
                            </li>
                        @endif
                    @endforeach
                </ol>
            </section>
        </div>

        {{-- Everything the user reported --}}
        <section x-data="{ show: 'all' }" aria-labelledby="items-heading" class="mt-14">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 id="items-heading" class="font-display text-2xl font-bold text-[#0F2A4A]">Your reports</h2>

                <div class="flex rounded-lg bg-[#EEF3F9] p-1 text-sm font-semibold" role="group" aria-label="Filter reports">
                    @foreach (['all' => 'All ('.count($items).')', 'lost' => "Lost ($lostCount)", 'found' => "Found ($foundCount)"] as $key => $label)
                        <button type="button" @click="show = '{{ $key }}'"
                                :aria-pressed="(show === '{{ $key }}').toString()"
                                :class="show === '{{ $key }}' ? 'bg-white text-[#0F2A4A] shadow-sm' : 'text-slate-600 hover:text-[#0F2A4A]'"
                                class="rounded-md px-3 py-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
                {{-- Column names (wide screens only) --}}
                <div class="hidden grid-cols-[minmax(0,1.3fr)_minmax(0,1.3fr)_6rem_11rem] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-sm font-medium text-slate-500 sm:grid" aria-hidden="true">
                    <span>Item</span><span>Where</span><span>Date</span><span>Status</span>
                </div>

                <ul class="divide-y divide-slate-100">
                    @foreach ($items as $item)
                        <li x-show="show === 'all' || show === '{{ $item['type'] }}'"
                            class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-4 gap-y-1 px-5 py-4 sm:grid-cols-[minmax(0,1.3fr)_minmax(0,1.3fr)_6rem_11rem] sm:items-center">

                            <p class="min-w-0">
                                <span class="block truncate font-semibold text-slate-800">{{ $item['name'] }}</span>
                                <span class="text-sm {{ $item['type'] === 'lost' ? 'text-slate-500' : 'text-amber-700' }}">
                                    {{ $item['type'] === 'lost' ? 'You lost this' : 'You found this' }}
                                </span>
                            </p>

                            <p class="col-start-1 row-start-2 truncate text-sm text-slate-600 sm:col-start-auto sm:row-start-auto">
                                {{ $item['place'] }}<span class="sm:hidden">, {{ $item['date']->format('M j') }}</span>
                            </p>

                            <p class="hidden text-sm text-slate-600 sm:block">{{ $item['date']->format('M j') }}</p>

                            {{-- Status: four bars for the four steps, plus the step name --}}
                            <div class="col-start-2 row-span-2 row-start-1 flex flex-col items-end gap-1.5 sm:col-start-auto sm:row-span-1 sm:row-start-auto sm:items-start">
                                <span class="flex gap-1" aria-hidden="true">
                                    @foreach ($steps as $i => $step)
                                        <span class="h-1.5 w-5 rounded-full {{ $i < $item['step'] || $item['step'] === 3 ? 'bg-[#0F2A4A]' : ($i === $item['step'] ? 'bg-[#F5B301]' : 'bg-slate-200') }}"></span>
                                    @endforeach
                                </span>
                                <span class="text-sm font-medium text-[#0F2A4A]">{{ $item['step'] === 2 ? 'Reserved' : $steps[$item['step']] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>
</x-app-layout>