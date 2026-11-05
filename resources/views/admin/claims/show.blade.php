<x-app-layout>
    <div class="mx-auto max-w-5xl px-6 py-10 sm:px-8">

        {{-- Back link --}}
        <div class="mb-6">
            <a href="{{ route('admin.claims.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-[#0F2A4A] transition-colors">
                &larr; Back to all claims
            </a>
        </div>

        {{-- Page Header --}}
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-5">
            <div>
                <h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A]">
                    Claim Review #{{ $claim->id }}
                </h1>
                <p class="mt-1 text-xs text-slate-400">
                    Submitted on {{ $claim->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
            <div>
                @if ($claim->status === 'Pending')
                    <span class="inline-flex rounded-md bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        Status: Pending Review
                    </span>
                @elseif ($claim->status === 'Approved')
                    <span class="inline-flex rounded-md bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        Status: Approved (Awaiting Pickup)
                    </span>
                @elseif ($claim->status === 'Rejected')
                    <span class="inline-flex rounded-md bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20">
                        Status: Rejected
                    </span>
                @else
                    <span class="inline-flex rounded-md bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-500/10">
                        Status: {{ $claim->status }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Alert messages --}}
        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

            {{-- Left: Associated Item Details --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-lg font-bold text-[#0F2A4A] mb-4">
                    Associated Item Details
                </h2>

                @if ($claim->report->photoUrl())
                    <div class="mb-5 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                        <img src="{{ $claim->report->photoUrl() }}" alt="{{ $claim->report->item_name }}" class="h-48 w-full object-contain">
                    </div>
                @endif

                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-400">Item Name:</dt>
                        <dd class="font-semibold text-slate-900">{{ $claim->report->item_name }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-400">Category:</dt>
                        <dd class="text-slate-800">{{ $claim->report->category }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-400">Location Found:</dt>
                        <dd class="text-slate-800">{{ $claim->report->place }}</dd>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <dt class="font-medium text-slate-400">Current Item Status:</dt>
                        <dd class="font-bold text-[#0F2A4A]">{{ $claim->report->status }}</dd>
                    </div>
                    <div class="pt-3">
                        <dt class="font-medium text-slate-400 mb-1">Finder Description:</dt>
                        <dd class="rounded-lg bg-slate-50 p-3 text-xs text-slate-700 leading-relaxed">
                            {{ $claim->report->description ?: 'No additional description provided by finder.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Right: Claimant, Proof & Decision --}}
            <div class="space-y-6">

                {{-- Claimant card --}}
                <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-display text-lg font-bold text-[#0F2A4A] mb-3">
                        Claimant Information
                    </h2>
                    <div class="text-sm">
                        <p class="font-semibold text-slate-900">{{ $claim->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $claim->user->email }}</p>
                    </div>

                    <div class="mt-5 border-t border-slate-100 pt-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Submitted Proof of Ownership
                        </h3>
                        <div class="mt-2 rounded-lg bg-slate-50 p-4 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap font-mono border border-slate-100">
{{ $claim->proof }}
                        </div>
                    </div>

                    @if ($claim->status === 'Rejected' && $claim->rejection_reason)
                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-800">
                            <span class="font-bold block">Rejection Reason:</span>
                            {{ $claim->rejection_reason }}
                        </div>
                    @endif
                </div>

                {{-- Decision Box (NO browser popups) --}}
                @if ($claim->status === 'Pending')
                    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="font-display text-lg font-bold text-[#0F2A4A]">
                            Administrator Decision
                        </h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Approving reserves the item for pickup. Rejecting requires a clear reason for the student.
                        </p>

                        <div class="mt-5 space-y-4">
                            {{-- Clean Approve Button (NO pop-up alert) --}}
                            <form method="POST" action="{{ route('admin.claims.approve', $claim) }}">
                                @csrf
                                <button type="submit"
                                        class="w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                                    Approve Claim (Reserve for Pickup)
                                </button>
                            </form>

                            {{-- Clean Reject Form --}}
                            <div class="border-t border-slate-100 pt-4">
                                <form method="POST" action="{{ route('admin.claims.reject', $claim) }}">
                                    @csrf
                                    <label for="rejection_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                                        Rejection Reason <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="2" required maxlength="500"
                                              placeholder="e.g. Proof description does not match unique item details."
                                              class="w-full rounded-lg border-slate-200 bg-slate-50 text-xs text-slate-800 focus:border-red-500 focus:ring-red-500">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')
                                        <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                    @enderror

                                    <button type="submit"
                                            class="mt-3 w-full rounded-lg border border-red-200 bg-white px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors">
                                        Reject Claim
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

        {{-- Other Competing Claims --}}
        @if ($otherClaims->isNotEmpty())
            <div class="mt-10 rounded-xl border border-amber-200 bg-amber-50/50 p-6">
                <h3 class="text-sm font-bold text-amber-900">
                    Other Claims on This Item ({{ $otherClaims->count() }})
                </h3>
                <p class="mt-0.5 text-xs text-amber-800">
                    Approving this claim will automatically mark other pending claims for this item as rejected.
                </p>
                <div class="mt-3 divide-y divide-amber-200/60 text-xs">
                    @foreach ($otherClaims as $other)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <span class="font-semibold text-amber-950">{{ $other->user->name }}</span>
                                <span class="text-amber-800/80 text-[11px]">({{ $other->status }})</span>
                            </div>
                            <a href="{{ route('admin.claims.show', $other) }}" class="font-semibold text-amber-900 underline hover:text-amber-950">
                                Review Claim #{{ $other->id }} &rarr;
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>