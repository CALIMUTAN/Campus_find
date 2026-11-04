<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    // How many item cards to show per page.
    private const PER_PAGE = 12;

    /**
     * Public catalog: browse and search lost & found reports.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => $request->query('type'),
            'category' => $request->query('category'),
            'place' => trim((string) $request->query('place', '')),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        $items = Report::query()
            // Claimed items are resolved, so they no longer belong in the public catalog.
            ->where('status', '!=', Report::STATUS_CLAIMED)
            ->when(
                in_array($filters['type'], ['lost', 'found'], true),
                fn ($query) => $query->where('type', $filters['type'])
            )
            ->when(
                in_array($filters['category'], Report::CATEGORIES, true),
                fn ($query) => $query->where('category', $filters['category'])
            )
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($query) use ($filters) {
                    $query->where('item_name', 'like', "%{$filters['q']}%")
                        ->orWhere('description', 'like', "%{$filters['q']}%");
                });
            })
            ->when(
                $filters['place'] !== '',
                fn ($query) => $query->where('place', 'like', "%{$filters['place']}%")
            )
            ->when(
                $this->isValidDate($filters['date_from']),
                fn ($query) => $query->whereDate('date', '>=', $filters['date_from'])
            )
            ->when(
                $this->isValidDate($filters['date_to']),
                fn ($query) => $query->whereDate('date', '<=', $filters['date_to'])
            )
            ->latest('date')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('items.index', [
            'items' => $items,
            'filters' => $filters,
            'categories' => Report::CATEGORIES,
        ]);
    }

    /**
     * Public detail page for a single report.
     */
    public function show(Report $report): View
    {
        return view('items.show', [
            'item' => $report,
            // Claimed items are kept viewable (so a direct link doesn't just 404)
            // but are flagged as no longer available instead of showing a claim CTA.
            'isAvailable' => $report->status !== Report::STATUS_CLAIMED,
        ]);
    }

    private function isValidDate(?string $value): bool
    {
        if (! $value) {
            return false;
        }

        return \DateTime::createFromFormat('Y-m-d', $value) !== false;
    }
}