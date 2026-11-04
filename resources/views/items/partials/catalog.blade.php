{{--
    Shared catalog content: search form + results grid.
    Included by both the guest page and the logged-in page so the
    two shells never drift out of sync.
--}}
@php
    $statusStyles = [
        \App\Models\Report::STATUS_REPORTED => 'bg-[#EEF3F9] text-[#0F2A4A]',
        \App\Models\Report::STATUS_PENDING_DROP_OFF => 'bg-[#FFF8E1] text-[#946200]',
        \App\Models\Report::STATUS_IN_CUSTODY => 'bg-[#0F2A4A] text-white',
        \App\Models\Report::STATUS_CLAIMED => 'bg-emerald-100 text-emerald-800',
    ];

    $hasFilters = collect($filters)->except(['type'])->filter(fn ($value) => filled($value))->isNotEmpty()
        || in_array($filters['type'], ['lost', 'found'], true);
@endphp

<h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A] sm:text-4xl">Browse lost &amp; found items</h1>
<p class="mt-2 max-w-2xl text-slate-600">Search every open report. Items already claimed are no longer listed here.</p>

{{-- Search & filter form --}}
<form method="GET" action="{{ route('items.index') }}" class="mt-8 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 sm:p-6">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
        <div class="sm:col-span-2 lg:col-span-2">
            <label for="q" class="text-sm font-bold text-[#0F2A4A]">Keyword</label>
            <input id="q" name="q" type="search" value="{{ $filters['q'] }}" maxlength="100"
                   placeholder="Item name or description"
                   class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
        </div>

        <div>
            <label for="type" class="text-sm font-bold text-[#0F2A4A]">Type</label>
            <select id="type" name="type" class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
                <option value="" @selected(! in_array($filters['type'], ['lost', 'found'], true))>All</option>
                <option value="lost" @selected($filters['type'] === 'lost')>Lost</option>
                <option value="found" @selected($filters['type'] === 'found')>Found</option>
            </select>
        </div>

        <div>
            <label for="category" class="text-sm font-bold text-[#0F2A4A]">Category</label>
            <select id="category" name="category" class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected($filters['category'] === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="place" class="text-sm font-bold text-[#0F2A4A]">Location</label>
            <input id="place" name="place" type="text" value="{{ $filters['place'] }}" maxlength="150"
                   placeholder="e.g. Library"
                   class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label for="date_from" class="text-sm font-bold text-[#0F2A4A]">From</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}"
                       max="{{ now()->toDateString() }}"
                       class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
            </div>
            <div>
                <label for="date_to" class="text-sm font-bold text-[#0F2A4A]">To</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}"
                       max="{{ now()->toDateString() }}"
                       class="mt-1.5 block w-full rounded-xl border-[#D5DEEA] bg-[#EEF3F9] text-slate-800 focus:border-[#0F2A4A] focus:bg-white focus:ring-[#0F2A4A]">
            </div>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button type="submit" class="rounded-xl bg-[#0F2A4A] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">
            Search
        </button>
        @if ($hasFilters)
            <a href="{{ route('items.index') }}" class="text-sm font-semibold text-[#0F2A4A] underline decoration-[#F5B301] decoration-2 underline-offset-4 hover:decoration-[#0F2A4A]">
                Clear filters
            </a>
        @endif
    </div>
</form>

{{-- Results --}}
<div class="mt-8">
    <p class="text-sm text-slate-500">
        {{ $items->total() }} {{ \Illuminate\Support\Str::plural('item', $items->total()) }} found
    </p>

    @if ($items->isEmpty())
        <div class="mt-4 flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white px-6 py-16 text-center">
            <svg class="h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <h2 class="mt-4 font-display text-lg font-bold text-[#0F2A4A]">No items match your search</h2>
            <p class="mt-1.5 max-w-sm text-sm text-slate-500">
                Try a different keyword, or clear the filters to see every open report.
            </p>
            @if ($hasFilters)
                <a href="{{ route('items.index') }}" class="mt-4 rounded-lg bg-[#0F2A4A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">
                    Clear filters
                </a>
            @endif
        </div>
    @else
        <ul class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                <li>
                    <a href="{{ route('items.show', $item) }}"
                       class="group block overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 transition hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]">
                        <div class="aspect-[4/3] w-full bg-[#EEF3F9]">
                            @if ($item->photoUrl())
                                <img src="{{ $item->photoUrl() }}" alt="Photo of {{ $item->item_name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-[#0F2A4A]/30">
                                    <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-bold uppercase tracking-wide {{ $item->type === 'lost' ? 'text-slate-500' : 'text-amber-700' }}">
                                    {{ $item->type === 'lost' ? 'Lost' : 'Found' }}
                                </span>
                                <span class="shrink-0 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusStyles[$item->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $item->status }}
                                </span>
                            </div>
                            <p class="mt-1 truncate font-display font-bold text-[#0F2A4A] group-hover:underline">{{ $item->item_name }}</p>
                            <p class="mt-0.5 truncate text-sm text-slate-500">{{ $item->category }} &middot; {{ $item->place }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $item->date->format('M j, Y') }}</p>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif
</div>