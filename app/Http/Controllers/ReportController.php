<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public const CATEGORIES = [
        'Bags',
        'Electronics',
        'IDs and wallets',
        'Clothing',
        'Keys',
        'Books',
        'Other',
    ];

    public function create(string $type): View
    {
        return view('reports.create', [
            'type' => $type,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $rules = [
            'item_name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'place' => ['required', 'string', 'max:150'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        if ($type === 'found') {
            $rules['drop_off'] = ['accepted'];
        }

        $data = $request->validate($rules, [
            'item_name.required' => 'Enter the name of the item.',
            'category.required' => 'Choose a category.',
            'category.in' => 'Choose a category from the list.',
            'place.required' => $type === 'lost'
                ? 'Enter where you lost it.'
                : 'Enter where you found it.',
            'date.required' => $type === 'lost'
                ? 'Enter when you lost it.'
                : 'Enter when you found it.',
            'date.before_or_equal' => 'The date can\'t be in the future.',
            'drop_off.accepted' => 'Found items must be dropped off at the Item Drop-Off. Check the box to continue.',
        ]);

        // Save the report for the currently logged-in user.
        $request->user()->reports()->create([
            'type' => $type,
            'item_name' => $data['item_name'],
            'category' => $data['category'],
            'place' => $data['place'],
            'date' => $data['date'],
            'description' => $data['description'] ?? null,
            'status' => 'Reported',
        ]);

        $message = $type === 'lost'
            ? "Your report for \"{$data['item_name']}\" was submitted successfully."
            : "Your report for \"{$data['item_name']}\" was submitted. Please drop the item off at the Item Drop-Off.";

        return redirect()
            ->route('dashboard')
            ->with('report_status', $message);
    }
}
