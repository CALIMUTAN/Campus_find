
<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:py-10">

        {{-- Page heading --}}
        <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">
            Notifications
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Stay updated on your item reports and ownership claims.
        </p>

        {{-- Notification list --}}
        <ul class="mt-6 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">

            @forelse ($notifications as $notification)

                @php
                    $data = $notification->data;
                @endphp

                <li class="flex flex-wrap items-start justify-between gap-3 px-5 py-5
                    {{ $notification->read_at ? 'bg-white' : 'bg-[#FFFDF5]' }}">

                    <div class="flex min-w-0 gap-3">

                        {{-- Unread indicator --}}
                        <div class="mt-1.5 shrink-0">
                            @if (!$notification->read_at)
                                <span class="block h-2.5 w-2.5 rounded-full bg-[#F5B301]"></span>
                            @else
                                <span class="block h-2.5 w-2.5 rounded-full bg-slate-200"></span>
                            @endif
                        </div>

                        <div class="min-w-0">

                            {{-- Notification title --}}
                            <h2 class="font-semibold text-slate-800">
                                {{ $data['title'] ?? 'Item Status Update' }}
                            </h2>

                            {{-- Notification message --}}
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">
                                {{ $data['message'] ?? 'You have a new update regarding your item.' }}
                            </p>

                            {{-- Item name --}}
                            @if (!empty($data['item_name']))
                                <p class="mt-2 text-sm font-medium text-[#0F2A4A]">
                                    Item: {{ $data['item_name'] }}
                                </p>
                            @endif

                            {{-- Date --}}
                            <p class="mt-2 text-xs text-slate-400">
                                {{ $notification->created_at->format('M j, Y - h:i A') }}
                            </p>

                        </div>
                    </div>

                    {{-- Read / unread status --}}
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold
                        {{ $notification->read_at
                            ? 'bg-slate-100 text-slate-600'
                            : 'bg-[#FFF8E1] text-[#946200]' }}">

                        {{ $notification->read_at ? 'Read' : 'Unread' }}

                    </span>

                </li>

            @empty

                <li class="px-5 py-12 text-center">

                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                        <svg
                            class="h-6 w-6 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true">

                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M10 21h4"/>

                        </svg>
                    </div>

                    <h2 class="mt-4 font-semibold text-slate-700">
                        No notifications yet
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        You'll see updates about your reports and claims here.
                    </p>

                </li>

            @endforelse

        </ul>

        {{-- Pagination --}}
        @if (method_exists($notifications, 'links'))
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
