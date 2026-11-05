<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Lost reports' => Report::where('type', 'lost')->count(),
                'Found items in custody' => Report::where('type', 'found')->where('status', Report::STATUS_IN_CUSTODY)->count(),
                'Pending claims' => Claim::where('status', Claim::STATUS_PENDING)->count(),
                'Approved, awaiting pickup' => Claim::where('status', Claim::STATUS_APPROVED)->count(),
                'Completed pickups' => Claim::where('status', Claim::STATUS_PICKED_UP)->count(),
            ],
            'recentReports' => Report::with('user')->latest()->take(5)->get(),
            'recentClaims' => Claim::with(['user', 'report'])->latest()->take(5)->get(),
        ]);
    }
}