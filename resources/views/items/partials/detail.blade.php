{{--
    Shared item detail content.
    Included by both the guest page and the logged-in page.
--}}
@php
    $statusStyles = [
        \App\Models\Report::STATUS_REPORTED => 'bg-[#EEF3F9] text-[#0F2A4A]',
        \App\Models\Report::STATUS_PENDING_DROP_OFF => 'bg-[#FFF8E1] text-[#946200]',
        \App\Models\Report::STATUS_IN_CUSTODY => 'bg-[#0F2A4A] text-white',
        \App\Models\Report::STATUS_CLAIMED => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0F2A4A] hover:underline">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
    Back to browse
</a>

@if (session('claim_error'))
    <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('claim_error') }}</div>
@endif

@unless ($isAvailable)
    <div class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-xs text-white" aria-hidden="true">&#10003;</span>
        <p>This item has already been claimed and is no longer available.</p>
    </div>
@endunless

<div class="mt-5 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 sm:grid sm:grid-cols-2">
    <div class="aspect-[4/3] w-full bg-[#EEF3F9] sm:aspect-auto sm:h-full">
        @if ($item->photoUrl())
            <img src="{{ $item->photoUrl() }}" alt="Photo of {{ $item->item_name }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full min-h-[14rem] w-full items-center justify-center text-[#0F2A4A]/30">
                <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
            </div>
        @endif
    </div>

    <div class="p-6 sm:p-8">
        <div class="flex items-start justify-between gap-2">
            <span class="text-xs font-bold uppercase tracking-wide {{ $item->type === 'lost' ? 'text-slate-500' : 'text-amber-700' }}">
                {{ $item->type === 'lost' ? 'Lost item' : 'Found item' }}
            </span>
            <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusStyles[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $item->status }}
            </span>
        </div>

        <h1 class="mt-2 font-display text-2xl font-bold text-[#0F2A4A] sm:text-3xl">{{ $item->item_name }}</h1>
        <p class="mt-1 text-sm font-medium text-slate-500">{{ $item->category }}</p>

        <dl class="mt-6 space-y-4 border-t border-slate-100 pt-5 text-sm">
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ $item->type === 'lost' ? 'Location lost' : 'Location found' }}</dt>
                <dd class="text-right font-semibold text-[#0F2A4A]">{{ $item->place }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-slate-500">{{ $item->type === 'lost' ? 'Date lost' : 'Date found' }}</dt>
                <dd class="text-right font-semibold text-[#0F2A4A]">{{ $item->date->format('M j, Y') }}</dd>
            </div>
        </dl>

        @if ($item->description)
            <div class="mt-5 border-t border-slate-100 pt-5">
                <h2 class="text-sm font-bold text-[#0F2A4A]">Description</h2>
                <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $item->description }}</p>
            </div>
        @endif

        @if ($isAvailable)
            <div class="mt-6 rounded-xl bg-[#EEF3F9] p-4">
                <p class="text-sm text-slate-600">
                    @if ($item->type === 'lost')
                        Found something like this? Let the owner know.
                    @else
                        Think this is yours? Submit a claim with proof of ownership.
                    @endif
                </p>
                @if ($item->type === 'lost')
                    <a href="{{ route('reports.create', 'found') }}" class="mt-3 inline-flex rounded-lg bg-[#0F2A4A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">
                        Report it as found
                                        </a>
                @endif
                @if ($item->type === 'found' && $item->status === \App\Models\Report::STATUS_IN_CUSTODY)
                    <a href="{{ auth()->check() ? route('claims.create', $item) : route('login') }}"
                       class="mt-3 inline-flex rounded-lg bg-[#0F2A4A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">
                        {{ auth()->check() ? 'Claim this item' : 'Log in to claim' }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>