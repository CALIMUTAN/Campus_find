<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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
        // Guard against duplicate decisions
        if ($claim->status !== Claim::STATUS_PENDING) {
            return back()->with('error', 'This claim has already been processed.');
        }

        DB::transaction(function () use ($claim) {
            // 1. Approve this claim
            $claim->update([
                'status' => Claim::STATUS_APPROVED,
            ]);

            // 2. Change the associated item to Reserved - Awaiting Pickup (FR-15)
            $claim->report->update([
                'status' => Report::STATUS_RESERVED,
            ]);

            // 3. Mark competing pending claims on the same item as Rejected - Item Assigned
            Claim::where('report_id', $claim->report_id)
                ->where('id', '!=', $claim->id)
                ->where('status', Claim::STATUS_PENDING)
                ->update([
                    'status' => Claim::STATUS_REJECTED,
                    'rejection_reason' => 'Rejected - Item has been assigned to an approved claimant.',
                ]);
        });

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

        return redirect()->route('admin.claims.index')
            ->with('status', 'Claim has been rejected.');
    }
}