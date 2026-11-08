@php
    $input = 'rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#0F2A4A] focus:ring-[#0F2A4A]';
@endphp

<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

        <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">
            Transaction Management
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Manage item drop-offs, school custody, and verified item pickups.
        </p>

        {{-- Success Message --}}
        @if (session('status'))
            <div role="status" class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Four Transaction Counts --}}
        <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pending Drop-offs</p>
                <p class="mt-2 text-3xl font-bold text-[#0F2A4A]">
                    {{ $counts['drop_off'] }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Items in Custody</p>
                <p class="mt-2 text-3xl font-bold text-[#0F2A4A]">
                    {{ $counts['custody'] }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Pending Pickups</p>
                <p class="mt-2 text-3xl font-bold text-[#0F2A4A]">
                    {{ $counts['pickup'] }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-slate-500">Completed Pickups</p>
                <p class="mt-2 text-3xl font-bold text-[#0F2A4A]">
                    {{ $counts['completed'] }}
                </p>
            </div>

        </div>

        {{-- 1. Pending Drop-offs --}}
        <section class="mt-10">
            <h2 class="text-xl font-bold text-[#0F2A4A]">
                1. Pending Drop-offs
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Found items waiting to be handed to the school admin.
            </p>

            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Reported By</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingDropOffs as $report)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $report->item_name }}
                                    <span class="block text-xs font-normal text-slate-500">
                                        {{ $report->category }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $report->user->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $report->place }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $report->date->format('M j, Y') }}
                                </td>

                                <td class="px-4 py-3">
                                    <form method="POST"
                                        action="{{ route('admin.transactions.dropoff', $report) }}"
                                        class="min-w-48 space-y-2">
                                        @csrf

                                        <textarea
                                            name="notes"
                                            rows="2"
                                            maxlength="1000"
                                            placeholder="Optional notes"
                                            class="{{ $input }} w-full"></textarea>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Confirm that this item has been physically received?')"
                                            class="rounded-lg bg-[#0F2A4A] px-3 py-2 text-xs font-semibold text-white hover:bg-[#16396a]">
                                            Confirm Drop-off
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                    No items are waiting for drop-off.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 2. Items in Custody --}}
        <section class="mt-10">
            <h2 class="text-xl font-bold text-[#0F2A4A]">
                2. Items in Custody
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Items physically received and currently stored by the school.
            </p>

            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Reported By</th>
                            <th class="px-4 py-3">Location Found</th>
                            <th class="px-4 py-3">Received Date</th>
                            <th class="px-4 py-3">Received By</th>
                            <th class="px-4 py-3">Notes</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($itemsInCustody as $transaction)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $transaction->report->item_name }}
                                    <span class="block text-xs font-normal text-slate-500">
                                        {{ $transaction->report->category }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->report->user->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->report->place }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->recorded_at?->format('M j, Y g:i A') ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->recorder->name ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->notes ?: 'No notes' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    No items are currently in school custody.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 3. Pending Pickups --}}
        <section class="mt-10">
            <h2 class="text-xl font-bold text-[#0F2A4A]">
                3. Pending Pickups
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Verify the claimant's school ID before releasing the item.
            </p>

            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Claimant</th>
                            <th class="px-4 py-3">Location</th>
                            <th class="px-4 py-3">Verification & Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pendingPickups as $claim)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $claim->report->item_name }}
                                    <span class="block text-xs font-normal text-slate-500">
                                        {{ $claim->report->category }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $claim->user->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $claim->report->place }}
                                </td>

                                <td class="px-4 py-3">
                                    <form method="POST"
                                        action="{{ route('admin.transactions.pickup', $claim) }}"
                                        class="min-w-56 space-y-3">
                                        @csrf

                                        <label class="flex items-start gap-2 text-xs text-slate-700">
                                            <input
                                                type="checkbox"
                                                name="identity_verified"
                                                value="1"
                                                required
                                                class="mt-0.5 rounded border-slate-300 text-[#0F2A4A] focus:ring-[#0F2A4A]">

                                            <span>
                                                I confirm that I checked the claimant's physical school ID.
                                            </span>
                                        </label>

                                        <textarea
                                            name="notes"
                                            rows="2"
                                            maxlength="1000"
                                            placeholder="Optional notes"
                                            class="{{ $input }} w-full"></textarea>

                                        <button
                                            type="submit"
                                            onclick="return confirm('Confirm that the item has been released to the verified claimant?')"
                                            class="rounded-lg bg-[#0F2A4A] px-3 py-2 text-xs font-semibold text-white hover:bg-[#16396a]">
                                            Confirm Pickup
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                    No items are waiting for pickup.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- 4. Completed Pickups --}}
        <section class="mt-10">
            <h2 class="text-xl font-bold text-[#0F2A4A]">
                4. Completed Pickups
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                History of items released to verified claimants.
            </p>

            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Claimant</th>
                            <th class="px-4 py-3">Pickup Date</th>
                            <th class="px-4 py-3">ID Verified</th>
                            <th class="px-4 py-3">Released By</th>
                            <th class="px-4 py-3">Notes</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($completedPickups as $transaction)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $transaction->report->item_name }}
                                    <span class="block text-xs font-normal text-slate-500">
                                        {{ $transaction->report->category }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->claim->user->name ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->recorded_at?->format('M j, Y g:i A') ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($transaction->identity_verified)
                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                            Verified
                                        </span>
                                    @else
                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                                            Not verified
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->recorder->name ?? 'N/A' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $transaction->notes ?: 'No notes' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    No completed pickups yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-app-layout>
