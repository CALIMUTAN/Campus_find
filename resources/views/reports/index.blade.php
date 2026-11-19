{{--
    "My reports" (fix/cf-016)
    Full, paginated list of everything the logged-in user has reported —
    the sidebar has linked here since cf-002, but the page never existed
    until now.
--}}
<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:py-10">
        <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">My reports</h1>
        <p class="mt-2 text-sm text-slate-500">Everything you've reported lost or found.</p>

        {{-- Type filter --}}
        <div class="mt-6 inline-flex rounded-lg bg-[#EEF3F9] p-1 text-sm font-semibold">
            @foreach (['' => 'All', 'lost' => 'Lost', 'found' => 'Found'] as $value => $label)
                <a href="{{ route('reports.index', $value !== '' ? ['type' => $value] : []) }}"
                   class="rounded-md px-3 py-1.5 {{ $type === $value || ($value === '' && ! in_array($type, ['lost', 'found'], true)) ? 'bg-white text-[#0F2A4A] shadow-sm' : 'text-slate-600 hover:text-[#0F2A4A]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <ul class="mt-4 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">
            @forelse ($reports as $report)
                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div class="min-w-0">
                        <a href="{{ route('items.show', $report) }}" class="block truncate font-semibold text-slate-800 hover:underline">{{ $report->item_name }}</a>
                        <p class="text-sm {{ $report->type === 'lost' ? 'text-slate-500' : 'text-amber-700' }}">
                            {{ $report->type === 'lost' ? 'You lost this' : 'You found this' }} &middot; {{ $report->category }}
                        </p>
                        <p class="text-sm text-slate-500">{{ $report->place }} &middot; {{ $report->date->format('M j, Y') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $report->statusBadgeClasses() }}">
                        {{ $report->status }}
                    </span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-slate-500">
                    @if (in_array($type, ['lost', 'found'], true))
                        No {{ $type }} reports yet.
                    @else
                        You haven't reported anything yet.
                    @endif
                </li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $reports->links() }}</div>
    </div>
</x-app-layout>
