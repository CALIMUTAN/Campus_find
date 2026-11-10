<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Notifications\ItemClaimNotification;
class ClaimController extends Controller
{
    /**
     * List all ownership claims for administrator review.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $claims = Claim::with(['user', 'report'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => Claim::count(),
            'pending' => Claim::where('status', Claim::STATUS_PENDING)->count(),
            'approved' => Claim::where('status', Claim::STATUS_APPROVED)->count(),
            'rejected' => Claim::where('status', Claim::STATUS_REJECTED)->count(),
        ];

        return view('admin.claims.index', compact('claims', 'counts', 'status'));
    }

    /**
     * Show a specific claim with its item details and competing claims.
     */
    public function show(Claim $claim): View
    {
        $claim->load(['user', 'report.user']);

        // Check for competing claims on the same item
        $otherClaims = Claim::with('user')
            ->where('report_id', $claim->report_id)
            ->where('id', '!=', $claim->id)
            ->get();

        return view('admin.claims.show', compact('claim', 'otherClaims'));
    }

    /**
     * Approve a claim and update the item status to Reserved.
     */
   
public function approve(Claim $claim): RedirectResponse
{
    // Prevent duplicate decisions.
    if ($claim->status !== Claim::STATUS_PENDING) {
        return back()->with('error', 'This claim has already been processed.');
    }

    $rejectedClaimIds = DB::transaction(function () use ($claim) {

        // 1. Approve the selected claim.
        $claim->update([
            'status' => Claim::STATUS_APPROVED,
        ]);

        // 2. Reserve the associated item.
        $claim->report->update([
            'status' => Report::STATUS_RESERVED,
        ]);

        // 3. Get competing pending claims before rejecting them.
        $rejectedClaimIds = Claim::where('report_id', $claim->report_id)
            ->where('id', '!=', $claim->id)
            ->where('status', Claim::STATUS_PENDING)
            ->pluck('id');

        // 4. Automatically reject competing claims.
        Claim::whereIn('id', $rejectedClaimIds)
            ->update([
                'status' => Claim::STATUS_REJECTED,
                'rejection_reason' => 'Rejected - Item has been assigned to an approved claimant.',
            ]);

        return $rejectedClaimIds;
    });

    // Send notification only after the transaction succeeds.
    $claim->load(['user', 'report']);

    $claim->user->notify(new ItemClaimNotification(
        event: 'claim_approved',
        title: 'Claim Approved',
        message: "Your claim for {$claim->report->item_name} has been approved. You can now arrange to pick up your item.",
        itemName: $claim->report->item_name,
        reportId: (int) $claim->report_id,
        claimId: (int) $claim->id
    ));

    // Notify users whose competing claims were automatically rejected.
    $rejectedClaims = Claim::with(['user', 'report'])
        ->whereIn('id', $rejectedClaimIds)
        ->get();

    foreach ($rejectedClaims as $rejectedClaim) {
        $rejectedClaim->user->notify(new ItemClaimNotification(
            event: 'claim_rejected',
            title: 'Claim Rejected',
            message: "Your claim for {$rejectedClaim->report->item_name} was rejected because the item has been assigned to another claimant.",
            itemName: $rejectedClaim->report->item_name,
            reportId: (int) $rejectedClaim->report_id,
            claimId: (int) $rejectedClaim->id
        ));
    }

    return redirect()->route('admin.claims.index')
        ->with('status', 'Claim approved successfully. The item is now marked as Reserved – Awaiting Pickup.');
}

    /**
     * Reject a claim with a mandatory reason.
     */
    public function reject(Request $request, Claim $claim): RedirectResponse
    {
        // Guard against duplicate decisions
        if ($claim->status !== Claim::STATUS_PENDING) {
            return back()->with('error', 'This claim has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Please provide a clear reason for rejecting this claim.',
        ]);

        DB::transaction(function () use ($claim, $validated) {
            $claim->update([
                'status' => Claim::STATUS_REJECTED,
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // If the item was previously reserved by this claim, return it to In Custody
            if ($claim->report->status === Report::STATUS_RESERVED) {
                $hasOtherApproved = Claim::where('report_id', $claim->report_id)
                    ->where('id', '!=', $claim->id)
                    ->where('status', Claim::STATUS_APPROVED)
                    ->exists();

                if (! $hasOtherApproved) {
                    $claim->report->update([
                        'status' => Report::STATUS_IN_CUSTODY,
                    ]);
                }
            }
        });
        // Send rejection notification after the transaction succeeds.
        $claim->load(['user', 'report']);

        $claim->user->notify(new ItemClaimNotification(
            event: 'claim_rejected',
            title: 'Claim Rejected',
            message: "Your claim for {$claim->report->item_name} was rejected. Reason: {$claim->rejection_reason}",
            itemName: $claim->report->item_name,
            reportId: (int) $claim->report_id,
            claimId: (int) $claim->id
        ));

        return redirect()->route('admin.claims.index')
            ->with('status', 'Claim has been rejected.');
    }
}