<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ItemTransaction;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ItemTransactionController extends Controller
{
    /**
     * Display all four item custody management sections.
     */
    public function index(): View
    {
        // 1. Items waiting for physical drop-off.
        $pendingDropOffs = Report::with('user')
            ->where('type', 'found')
            ->where('status', Report::STATUS_PENDING_DROP_OFF)
            ->latest()
            ->get();

        // 2. Items physically received and still in school custody.
        $itemsInCustody = ItemTransaction::with([
                'report.user',
                'recorder',
            ])
            ->where('type', ItemTransaction::TYPE_DROP_OFF)
            ->whereHas('report', function ($query) {
                $query->where('status', Report::STATUS_IN_CUSTODY);
            })
            ->latest('recorded_at')
            ->get();

        // 3. Approved claims waiting for physical pickup.
        $pendingPickups = Claim::with(['user', 'report'])
            ->where('status', Claim::STATUS_APPROVED)
            ->whereHas('report', function ($query) {
                $query->where('status', Report::STATUS_RESERVED);
            })
            ->latest()
            ->get();

        // 4. Items already released to verified claimants.
        $completedPickups = ItemTransaction::with([
                'report',
                'claim.user',
                'recorder',
            ])
            ->where('type', ItemTransaction::TYPE_PICKUP)
            ->latest('recorded_at')
            ->get();

        // Summary counts for all four sections.
        $counts = [
            'drop_off' => $pendingDropOffs->count(),
            'custody' => $itemsInCustody->count(),
            'pickup' => $pendingPickups->count(),
            'completed' => $completedPickups->count(),
        ];

        return view('admin.transactions.index', compact(
            'pendingDropOffs',
            'itemsInCustody',
            'pendingPickups',
            'completedPickups',
            'counts'
        ));
    }

    /**
     * Record the physical receipt of a found item.
     */
    public function recordDropOff(
        Request $request,
        Report $report
    ): RedirectResponse {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $validated) {

            $lockedReport = Report::whereKey($report->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedReport->type !== 'found' ||
                $lockedReport->status !== Report::STATUS_PENDING_DROP_OFF
            ) {
                throw ValidationException::withMessages([
                    'drop_off' => 'This item is not awaiting drop-off or has already been received.',
                ]);
            }

            if (ItemTransaction::where('report_id', $lockedReport->id)
                ->where('type', ItemTransaction::TYPE_DROP_OFF)
                ->exists()) {
                throw ValidationException::withMessages([
                    'drop_off' => 'A drop-off transaction already exists for this item.',
                ]);
            }

            ItemTransaction::create([
                'report_id' => $lockedReport->id,
                'claim_id' => null,
                'recorded_by' => auth()->id(),
                'type' => ItemTransaction::TYPE_DROP_OFF,
                'identity_verified' => null,
                'notes' => $validated['notes'] ?? null,
                'recorded_at' => now(),
            ]);

            $lockedReport->update([
                'status' => Report::STATUS_IN_CUSTODY,
            ]);
        });

        return redirect()->route('admin.transactions.index')
            ->with('status', 'Item drop-off recorded successfully. The item is now in school custody.');
    }

    /**
     * Verify the claimant's physical school ID
     * and confirm the item pickup.
     */
    public function confirmPickup(
        Request $request,
        Claim $claim
    ): RedirectResponse {
        $validated = $request->validate([
            'identity_verified' => ['required', 'accepted'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'identity_verified.required' => 'Please confirm that you checked the claimant\'s school ID.',
            'identity_verified.accepted' => 'You must verify the claimant\'s school ID before releasing the item.',
        ]);

        DB::transaction(function () use ($claim, $validated) {

            $lockedClaim = Claim::whereKey($claim->id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedReport = Report::whereKey($lockedClaim->report_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedClaim->status !== Claim::STATUS_APPROVED ||
                $lockedReport->status !== Report::STATUS_RESERVED
            ) {
                throw ValidationException::withMessages([
                    'pickup' => 'This claim is not awaiting pickup or has already been processed.',
                ]);
            }

            if (ItemTransaction::where('report_id', $lockedReport->id)
                ->where('type', ItemTransaction::TYPE_PICKUP)
                ->exists()) {
                throw ValidationException::withMessages([
                    'pickup' => 'A pickup transaction already exists for this item.',
                ]);
            }

            if (ItemTransaction::where('claim_id', $lockedClaim->id)->exists()) {
                throw ValidationException::withMessages([
                    'pickup' => 'This claim already has a pickup transaction.',
                ]);
            }

            ItemTransaction::create([
                'report_id' => $lockedReport->id,
                'claim_id' => $lockedClaim->id,
                'recorded_by' => auth()->id(),
                'type' => ItemTransaction::TYPE_PICKUP,
                'identity_verified' => true,
                'notes' => $validated['notes'] ?? null,
                'recorded_at' => now(),
            ]);

            $lockedClaim->update([
                'status' => Claim::STATUS_PICKED_UP,
            ]);

            $lockedReport->update([
                'status' => Report::STATUS_CLAIMED,
            ]);
        });

        return redirect()->route('admin.transactions.index')
            ->with('status', 'Pickup confirmed successfully. The item has been released to the verified claimant.');
    }
}
