<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Handles the "I lost an item" and "I found an item" buttons on the dashboard.
 * (feat/cf-001/add-user-dashboard-routing)
 *
 * {type} in the URL is always "lost" or "found" (checked in routes/web.php).
 */
class ReportController extends Controller
{
    /** Same categories as the front page. */
    public const CATEGORIES = ['Bags', 'Electronics', 'IDs and wallets', 'Clothing', 'Keys', 'Books', 'Other'];

    /**
     * Show the report form.
     * GET /report/lost  or  GET /report/found
     */
    public function create(string $type): View
    {
        return view('reports.create', [
            'type' => $type,
            'categories' => self::CATEGORIES,
        ]);
    }

    /**
     * Check the form and send the user back to the dashboard.
     * POST /report/lost  or  POST /report/found
     */
    public function store(Request $request, string $type): RedirectResponse
    {
        // FR-05 / FR-06: item details for lost and found reports
        $rules = [
            'item_name'   => ['required', 'string', 'max:100'],
            'category'    => ['required', Rule::in(self::CATEGORIES)],
            'place'       => ['required', 'string', 'max:150'],
            'date'        => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        // FR-07: found items must be dropped off at the Item Drop-Off
        if ($type === 'found') {
            $rules['drop_off'] = ['accepted'];
        }

        $data = $request->validate($rules, [
            'item_name.required'    => 'Enter the name of the item.',
            'category.required'     => 'Choose a category.',
            'category.in'           => 'Choose a category from the list.',
            'place.required'        => $type === 'lost' ? 'Enter where you lost it.' : 'Enter where you found it.',
            'date.required'         => $type === 'lost' ? 'Enter when you lost it.' : 'Enter when you found it.',
            'date.before_or_equal'  => 'The date can\'t be in the future.',
            'drop_off.accepted'     => 'Found items must be dropped off at the Item Drop-Off. Check the box to continue.',
        ], [
            'item_name' => 'item name',
        ]);

        // TODO (database branch): save the report here, tied to the logged-in user (FR-02), for example:
        // $request->user()->reports()->create(Arr::except($data, 'drop_off') + ['type' => $type, 'status' => 'Reported']);
        // A found item stays "Reported" until an admin records the drop-off (FR-16), then it becomes "In custody".

        $message = $type === 'lost'
            ? "Your report for \"{$data['item_name']}\" was sent. You'll get a notification if a matching item is dropped off."
            : "Your report for \"{$data['item_name']}\" was sent. Please drop the item off at the Item Drop-Off so staff can record it.";

        return redirect()->route('dashboard')->with('report_status', $message);
    }
}