<x-app-layout>
    <div class="mx-auto max-w-6xl px-6 py-10 sm:px-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A]">
                Ownership Claims Review
            </h1>
            <p class="mt-1.5 text-sm text-slate-500">
                Review submitted proof of ownership from students and manage claim decisions.
            </p>
        </div>

        {{-- Alerts --}}
        @if (session('status'))
            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter Buttons with uniform neutral gray badges --}}
        <div class="mb-6 flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.claims.index') }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ ! $status ? 'bg-[#0F2A4A] text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                <span>All</span>
                <span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ ! $status ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
            </a>

            <a href="{{ route('admin.claims.index', ['status' => 'Pending']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $status === 'Pending' ? 'bg-[#0F2A4A] text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                <span>Pending</span>
                <span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ $status === 'Pending' ? 'bg-[#0F2A4A]/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['pending'] }}</span>
            </a>

            <a href="{{ route('admin.claims.index', ['status' => 'Approved']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $status === 'Approved' ? 'bg-[#0F2A4A] text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                <span>Approved</span>
                <span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ $status === 'Approved' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['approved'] }}</span>
            </a>

            <a href="{{ route('admin.claims.index', ['status' => 'Rejected']) }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $status === 'Rejected' ? 'bg-[#0F2A4A] text-white shadow-sm' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                <span>Rejected</span>
                <span class="rounded px-1.5 py-0.5 text-xs font-semibold {{ $status === 'Rejected' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['rejected'] }}</span>
            </a>
        </div>

        {{-- Table Container with spacious top & bottom padding for the empty state --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            @if ($claims->isEmpty())
                <div class="py-16 sm:py-20 px-6 text-center">
                    <p class="font-display text-base font-semibold text-slate-700">No claims found.</p>
                    <p class="mt-2 text-xs text-slate-400">There are currently no claims matching this filter.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="border-b border-slate-100 bg-white text-xs font-semibold text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Claimant</th>
                                <th class="px-6 py-4">Item</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($claims as $claim)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900">{{ $claim->user->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $claim->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-900">{{ $claim->report->item_name }}</div>
                                        <div class="text-xs text-slate-400">{{ $claim->report->category }} &bull; {{ $claim->report->place }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500">
                                        {{ $claim->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($claim->status === 'Pending')
                                            <span class="inline-flex rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                Pending
                                            </span>
                                        @elseif ($claim->status === 'Approved')
                                            <span class="inline-flex rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                Approved
                                            </span>
                                        @elseif ($claim->status === 'Rejected')
                                            <span class="inline-flex rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                                {{ $claim->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.claims.show', $claim) }}"
                                           class="font-semibold text-[#0F2A4A] hover:underline">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($claims->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $claims->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>