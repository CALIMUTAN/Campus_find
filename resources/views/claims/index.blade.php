@php
    $styles = [
        'Pending' => 'bg-[#FFF8E1] text-[#946200]',
        'Approved' => 'bg-[#0F2A4A] text-white',
        'Rejected' => 'bg-red-100 text-red-800',
        'Picked Up' => 'bg-emerald-100 text-emerald-800',
    ];
    $labels = ['Approved' => 'Approved, ready for pickup'];
@endphp
<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:py-10">
        <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">My claims</h1>

        @if (session('claim_status'))
            <div role="status" class="mt-5 rounded-xl border border-[#F5B301] bg-[#FFF8E1] px-4 py-3 text-sm text-[#0F2A4A]">{{ session('claim_status') }}</div>
        @endif

        <ul class="mt-6 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white">
            @forelse ($claims as $claim)
                <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                    <div class="min-w-0">
                        <a href="{{ route('items.show', $claim->report) }}" class="block truncate font-semibold text-slate-800 hover:underline">{{ $claim->report->item_name }}</a>
                        <p class="text-sm text-slate-500">{{ $claim->report->category }} &middot; found at {{ $claim->report->place }}</p>
                        <p class="text-sm text-slate-500">Submitted {{ $claim->created_at->format('M j, Y') }}</p>
                    </div>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $styles[$claim->status] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ $labels[$claim->status] ?? $claim->status }}
                    </span>
                </li>
            @empty
                <li class="px-5 py-10 text-center text-sm text-slate-500">You haven't submitted any claims yet.</li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $claims->links() }}</div>
    </div>
</x-app-layout>