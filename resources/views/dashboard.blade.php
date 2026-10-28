
@php
    $user = Auth::user();

    $steps = [
        'Reported',
        'In custody',
        'Reserved, awaiting pickup',
        'Claimed',
    ];

    $lostCount = $items->where('type', 'lost')->count();
    $foundCount = $items->where('type', 'found')->count();

    $pickup = $items->firstWhere('status', 'Reserved, awaiting pickup');
@endphp

<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A]">
                Dashboard
            </h1>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('reports.create', 'lost') }}"
                   class="rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a]">
                    I lost an item
                </a>

                <a href="{{ route('reports.create', 'found') }}"
                   class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-[#0F2A4A] hover:bg-slate-50">
                    I found an item
                </a>
            </div>
        </div>

        @if (session('report_status'))
            <div x-data="{ show: true }" x-show="show"
                 role="status"
                 class="mt-6 flex items-start gap-3 rounded-xl border border-[#F5B301] bg-[#FFF8E1] px-4 py-3 text-sm text-[#0F2A4A]">
                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#F5B301] text-xs">
                    &#10003;
                </span>

                <p class="flex-1">{{ session('report_status') }}</p>

                <button type="button" @click="show = false"
                        class="text-slate-500 hover:text-[#0F2A4A]"
                        aria-label="Close message">
                    &times;
                </button>
            </div>
        @endif

        <div class="mt-10 grid grid-cols-1 items-start gap-12 md:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] lg:gap-16">

            {{-- Student profile --}}
            <section aria-labelledby="profile-name" class="px-2 sm:px-0">
                <div class="relative -rotate-2 rounded-3xl bg-[#F5B301] p-1.5 shadow-xl shadow-[#0F2A4A]/10">
                    <div class="rounded-[1.4rem] bg-white px-6 pb-6 pt-11">
                        <span class="absolute left-1/2 top-5 h-4 w-4 -translate-x-1/2 rounded-full bg-[#0F2A4A]"></span>

                        <p class="text-sm text-slate-500">Student</p>

                        <h2 id="profile-name"
                            class="font-display text-2xl font-bold leading-tight text-[#0F2A4A]">
                            {{ $user->name }}
                        </h2>

                        <p class="mt-1 break-all text-sm text-slate-500">
                            {{ $user->email }}
                        </p>

                        <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-dashed border-slate-300 pt-5 text-sm">
                            <div>
                                <dt class="text-slate-500">Joined</dt>
                                <dd class="font-semibold text-[#0F2A4A]">
                                    {{ optional($user->created_at)->format('M Y') ?? 'Not set' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-slate-500">Email</dt>
                                <dd class="font-semibold {{ $user->email_verified_at ? 'text-[#0F2A4A]' : 'text-amber-700' }}">
                                    {{ $user->email_verified_at ? 'Verified' : 'Not verified' }}
                                </dd>
                            </div>
                        </dl>

                        <a href="{{ route('profile.edit') }}"
                           class="mt-6 inline-block text-sm font-semibold text-[#0F2A4A] underline decoration-[#F5B301] decoration-2 underline-offset-4">
                            Edit profile
                        </a>
                    </div>
                </div>
            </section>

            {{-- Pickup information appears only for a matching report --}}
            <section aria-labelledby="pickup-heading" class="md:pt-4">
                @if ($pickup)
                    <h2 id="pickup-heading"
                        class="font-display text-2xl font-bold text-[#0F2A4A]">
                        Your {{ strtolower($pickup->item_name) }} is ready for pickup
                    </h2>

                    <p class="mt-2 max-w-prose text-slate-600">
                        Go to the Lost and Found office and bring your school ID.
                        Staff will check it before handing the item over.
                    </p>

                    <p class="mt-4 text-sm font-semibold text-[#0F2A4A]">
                        Status: {{ $pickup->status }}
                    </p>
                @else
                    <h2 id="pickup-heading"
                        class="font-display text-2xl font-bold text-[#0F2A4A]">
                        Pickup updates
                    </h2>

                    <p class="mt-2 max-w-prose text-slate-600">
                        When one of your reports is ready for pickup, its status will appear here.
                    </p>
                @endif
            </section>
        </div>

        {{-- Reports from the database --}}
        <section x-data="{ show: 'all' }"
                 aria-labelledby="items-heading"
                 class="mt-14">

            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 id="items-heading"
                    class="font-display text-2xl font-bold text-[#0F2A4A]">
                    Your reports
                </h2>

                <div class="flex rounded-lg bg-[#EEF3F9] p-1 text-sm font-semibold"
                     role="group" aria-label="Filter reports">

                    @foreach ([
                        'all' => 'All (' . $items->count() . ')',
                        'lost' => 'Lost (' . $lostCount . ')',
                        'found' => 'Found (' . $foundCount . ')',
                    ] as $key => $label)
                        <button type="button"
                                @click="show = '{{ $key }}'"
                                :aria-pressed="(show === '{{ $key }}').toString()"
                                :class="show === '{{ $key }}'
                                    ? 'bg-white text-[#0F2A4A] shadow-sm'
                                    : 'text-slate-600 hover:text-[#0F2A4A]'"
                                class="rounded-md px-3 py-1.5">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">

                <div class="hidden grid-cols-[minmax(0,1.3fr)_minmax(0,1.3fr)_6rem_11rem] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-sm font-medium text-slate-500 sm:grid">
                    <span>Item</span>
                    <span>Where</span>
                    <span>Date</span>
                    <span>Status</span>
                </div>

                @if ($items->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <h3 class="font-semibold text-[#0F2A4A]">
                            No reports yet
                        </h3>

                        <p class="mt-2 text-sm text-slate-600">
                            Use the buttons above to report a lost or found item.
                        </p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($items as $item)
                            <li x-show="show === 'all' || show === '{{ $item->type }}'"
                                class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-4 gap-y-1 px-5 py-4 sm:grid-cols-[minmax(0,1.3fr)_minmax(0,1.3fr)_6rem_11rem] sm:items-center">

                                <p class="min-w-0">
                                    <span class="block truncate font-semibold text-slate-800">
                                        {{ $item->item_name }}
                                    </span>

                                    <span class="text-sm {{ $item->type === 'lost' ? 'text-slate-500' : 'text-amber-700' }}">
                                        {{ $item->type === 'lost' ? 'You lost this' : 'You found this' }}
                                    </span>
                                </p>

                                <p class="col-start-1 row-start-2 truncate text-sm text-slate-600 sm:col-start-auto sm:row-start-auto">
                                    {{ $item->place }}

                                    <span class="sm:hidden">
                                        , {{ $item->date->format('M j') }}
                                    </span>
                                </p>

                                <p class="hidden text-sm text-slate-600 sm:block">
                                    {{ $item->date->format('M j') }}
                                </p>

                                <div class="col-start-2 row-span-2 row-start-1 flex flex-col items-end gap-1.5 sm:col-start-auto sm:row-span-1 sm:row-start-auto sm:items-start">
                                    <span class="text-sm font-medium text-[#0F2A4A]">
                                        {{ $item->status }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>
