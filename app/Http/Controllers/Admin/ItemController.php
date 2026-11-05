<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'category' => $request->query('category'),
        ];

        $items = Report::with('user')
            ->when($filters['q'] !== '', fn ($query) => $query->where(function ($query) use ($filters) {
                $query->where('item_name', 'like', "%{$filters['q']}%")
                    ->orWhere('place', 'like', "%{$filters['q']}%")
                    ->orWhere('description', 'like', "%{$filters['q']}%");
            }))
            ->when(in_array($filters['type'], ['lost', 'found'], true), fn ($query) => $query->where('type', $filters['type']))
            ->when(in_array($filters['status'], Report::STATUSES, true), fn ($query) => $query->where('status', $filters['status']))
            ->when(in_array($filters['category'], Report::CATEGORIES, true), fn ($query) => $query->where('category', $filters['category']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.items.index', [
            'items' => $items,
            'filters' => $filters,
            'categories' => Report::CATEGORIES,
            'statuses' => Report::STATUSES,
        ]);
    }

    public function edit(Report $report): View
    {
        return view('admin.items.edit', [
            'item' => $report->load('user'),
            'categories' => Report::CATEGORIES,
            'statuses' => $this->allowedStatuses($report),
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        // Only fields in this list can change: type and owner are never editable.
        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(Report::CATEGORIES)],
            'place' => ['required', 'string', 'max:150'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in($this->allowedStatuses($report))],
        ]);

        if ($report->status === Report::STATUS_CLAIMED && $data['status'] !== Report::STATUS_CLAIMED) {
            return back()->withInput()->withErrors(['status' => 'A claimed item can\'t be reopened.']);
        }

        if ($data['status'] !== Report::STATUS_IN_CUSTODY
            && $report->claims()->whereIn('status', Claim::ACTIVE)->exists()) {
            return back()->withInput()->withErrors(['status' => 'This item has active claims. Resolve them before changing its status.']);
        }

        $report->update($data);

        return redirect()->route('admin.items.index')
            ->with('admin_status', "\"{$report->item_name}\" was updated.");
    }

    // A lost report can't be "In Custody", and a found item can't be just "Reported".
    private function allowedStatuses(Report $report): array
    {
        return $report->type === 'lost'
            ? [Report::STATUS_REPORTED, Report::STATUS_CLAIMED]
            : [Report::STATUS_PENDING_DROP_OFF, Report::STATUS_IN_CUSTODY, Report::STATUS_CLAIMED];
    }
}