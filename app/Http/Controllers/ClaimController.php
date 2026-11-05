<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClaimController extends Controller
{
    // Claim history: only the logged-in user's own claims, never anyone else's.
    public function index(Request $request): View
    {
        $claims = $request->user()->claims()->with('report')->latest()->paginate(10);

        return view('claims.index', ['claims' => $claims]);
    }

    public function create(Request $request, Report $report): View|RedirectResponse
    {
        if ($reason = $this->ineligibleReason($request->user(), $report)) {
            return redirect()->route('items.show', $report)->with('claim_error', $reason);
        }

        return view('claims.create', ['item' => $report]);
    }

    public function store(Request $request, Report $report): RedirectResponse
    {
        $data = $request->validate([
            'proof' => ['required', 'string', 'min:20', 'max:1000'],
        ], [
            'proof.required' => 'Describe how you can prove this item is yours.',
            'proof.min' => 'Please give more detail (at least 20 characters).',
            'proof.max' => 'Keep your description to 1000 characters or fewer.',
        ]);

        // Re-check eligibility inside a transaction so two quick submits can't both pass.
        $error = DB::transaction(function () use ($request, $report, $data) {
            $user = $request->user();

            if ($reason = $this->ineligibleReason($user, $report)) {
                return $reason;
            }

            $user->claims()->create([
                'report_id' => $report->id,
                'proof' => $data['proof'],
                'status' => Claim::STATUS_PENDING,
            ]);

            return null;
        });

        if ($error) {
            return redirect()->route('items.show', $report)->with('claim_error', $error);
        }

        return redirect()->route('claims.index')
            ->with('claim_status', "Your claim for \"{$report->item_name}\" was submitted. Staff will review it and update the status here.");
    }

    private function ineligibleReason(User $user, Report $report): ?string
    {
        if ($report->type !== 'found') {
            return 'Only found items can be claimed.';
        }
        if ($report->status !== Report::STATUS_IN_CUSTODY) {
            return 'This item is not available for claims right now.';
        }
        if ($report->user_id === $user->id) {
            return 'You reported this item, so you can\'t claim it.';
        }
        if ($report->claims()->where('user_id', $user->id)->whereIn('status', Claim::ACTIVE)->exists()) {
            return 'You already have an active claim for this item.';
        }
        if ($report->claims()->where('status', Claim::STATUS_APPROVED)->exists()) {
            return 'This item is already reserved for another claimant.';
        }

        return null;
    }
}