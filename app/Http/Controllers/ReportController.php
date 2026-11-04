<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

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

    // Keep uploads small and fast, especially for students on mobile data.
    private const MAX_PHOTO_KILOBYTES = 2048;

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
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:'.self::MAX_PHOTO_KILOBYTES],
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
            'photo.image' => 'The photo must be a JPG or PNG image.',
            'photo.mimes' => 'The photo must be a JPG or PNG image.',
            'photo.max' => 'The photo must be 2MB or smaller.',
            'drop_off.accepted' => 'Found items must be dropped off at the Item Drop-Off. Check the box to continue.',
        ]);

        // Upload the photo (if any) before touching the database, so a storage
        // failure never leaves behind a half-written report.
        try {
            $photoPath = $request->hasFile('photo')
                ? $request->file('photo')->store('reports/photos', 'public')
                : null;
        } catch (Throwable $e) {
            Log::error('Failed to store report photo.', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['photo' => 'We couldn\'t upload that photo. Please try again.']);
        }

        try {
            $request->user()->reports()->create([
                'type' => $type,
                'item_name' => $data['item_name'],
                'category' => $data['category'],
                'place' => $data['place'],
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
                'photo_path' => $photoPath,
                // Found items wait here until staff confirm the item was
                // actually dropped off and taken into custody.
                'status' => $type === 'found'
                    ? Report::STATUS_PENDING_DROP_OFF
                    : Report::STATUS_REPORTED,
            ]);
        } catch (Throwable $e) {
            // Don't leave an orphaned file behind if the report failed to save.
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            Log::error('Failed to save report.', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors(['item_name' => 'Something went wrong while saving your report. Please try again.']);
        }

        $message = $type === 'lost'
            ? "Your report for \"{$data['item_name']}\" was submitted successfully."
            : "Your report for \"{$data['item_name']}\" was submitted. Please drop the item off at the Item Drop-Off within 3 school days.";

        return redirect()
            ->route('dashboard')
            ->with('report_status', $message);
    }
}