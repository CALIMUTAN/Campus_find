<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ItemTransaction;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin reporting: lost reports, found items, claims, drop-offs and pickups.
 *
 * The on-screen table, the CSV and the PDF all come from the same dataset()
 * builder, so the three outputs can never disagree with each other or with
 * the database.
 *
 * Access is restricted by the 'admin' middleware on the route group.
 */
class ReportController extends Controller
{
    private const TYPES = [
        'lost'      => 'Lost item reports',
        'found'     => 'Found items',
        'claims'    => 'Ownership claims',
        'drop_offs' => 'Drop-offs',
        'pickups'   => 'Pickups',
    ];

    // PDFs are built in memory, so cap them. CSV has no cap (it streams).
    private const PDF_LIMIT = 2000;

    public function index(Request $request)
    {
        $f = $this->filters($request);
        $set = $this->dataset($f);

        $paginator = $set['query']->paginate(15)->withQueryString();
        $rows = $paginator->getCollection()->map($set['row'])->all();

        return view('admin.reports.index', [
            'filters'       => $f,
            'types'         => self::TYPES,
            'title'         => $set['title'],
            'headers'       => $set['headers'],
            'rows'          => $rows,
            'paginator'     => $paginator,
            'statusOptions' => $this->statusOptions($f['type']),
        ]);
    }

    public function csv(Request $request): StreamedResponse
    {
        $f = $this->filters($request);
        $set = $this->dataset($f);

        $filename = 'campus-find-' . str_replace('_', '-', $f['type'])
            . '-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($set) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accents correctly
            fputcsv($out, $set['headers']);

            foreach ($set['query']->lazy(500) as $record) {
                fputcsv($out, array_map([$this, 'safeCell'], ($set['row'])($record)));
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function pdf(Request $request)
    {
        $f = $this->filters($request);
        $set = $this->dataset($f);

        $total = (clone $set['query'])->count();
        $rows = $set['query']->limit(self::PDF_LIMIT)->get()->map($set['row'])->all();

        $filename = 'campus-find-' . str_replace('_', '-', $f['type'])
            . '-report-' . now()->format('Ymd-His') . '.pdf';

        return Pdf::loadView('admin.reports.pdf', [
            'title'       => $set['title'],
            'headers'     => $set['headers'],
            'rows'        => $rows,
            'total'       => $total,
            'truncated'   => $total > self::PDF_LIMIT,
            'limit'       => self::PDF_LIMIT,
            'filters'     => $f,
            'statusLabel' => $this->statusOptions($f['type'])[$f['status'] ?? ''] ?? null,
            'generatedBy' => $request->user()->name,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')->download($filename);
    }

    /**
     * Validate and normalise the filter inputs.
     */
    private function filters(Request $request): array
    {
        $data = $request->validate([
            'type'   => ['nullable', Rule::in(array_keys(self::TYPES))],
            'status' => ['nullable', 'string', 'max:50'],
            'from'   => ['nullable', 'date'],
            'to'     => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $data['type'] ??= 'lost';

        // Drop a status that doesn't belong to the chosen report type.
        if (! array_key_exists($data['status'] ?? '', $this->statusOptions($data['type']))) {
            $data['status'] = null;
        }

        return $data;
    }

    private function statusOptions(string $type): array
    {
        return match ($type) {
            'claims' => $this->same([
                Claim::STATUS_PENDING,
                Claim::STATUS_APPROVED,
                Claim::STATUS_REJECTED,
                Claim::STATUS_PICKED_UP,
            ]),
            'drop_offs', 'pickups' => [
                'verified'   => 'Identity verified',
                'unverified' => 'Not verified',
            ],
            default => $this->same(Report::STATUSES),
        };
    }

    private function same(array $values): array
    {
        return array_combine($values, $values);
    }

    /**
     * Build the query, column headers and row mapper for one report type,
     * with the date-range and status filters applied.
     */
    private function dataset(array $f): array
    {
        $type = $f['type'];

        switch ($type) {
            case 'claims':
                $query = Claim::with(['user:id,name,email', 'report:id,item_name']);
                $dateColumn = 'claims.created_at';
                $headers = ['ID', 'Item', 'Claimant', 'Email', 'Status', 'Rejection reason', 'Submitted'];
                $row = fn (Claim $c) => [
                    $c->id,
                    $c->report?->item_name,
                    $c->user?->name,
                    $c->user?->email,
                    $c->status,
                    $c->rejection_reason,
                    $c->created_at?->format('Y-m-d H:i'),
                ];
                break;

            case 'drop_offs':
            case 'pickups':
                $query = ItemTransaction::with(['report:id,item_name', 'claim.user:id,name', 'recorder:id,name'])
                    ->where('type', $type === 'drop_offs'
                        ? ItemTransaction::TYPE_DROP_OFF
                        : ItemTransaction::TYPE_PICKUP);
                $dateColumn = 'item_transactions.recorded_at';
                $headers = ['ID', 'Item', 'Claimant', 'Recorded by', 'Identity verified', 'Notes', 'Recorded at'];
                $row = fn (ItemTransaction $t) => [
                    $t->id,
                    $t->report?->item_name,
                    $t->claim?->user?->name,
                    $t->recorder?->name,
                    $t->identity_verified ? 'Yes' : 'No',
                    $t->notes,
                    $t->recorded_at?->format('Y-m-d H:i'),
                ];
                break;

            default: // 'lost' | 'found'
                $query = Report::with('user:id,name,email')->where('type', $type);
                $dateColumn = 'reports.created_at';
                $headers = ['ID', 'Item', 'Category', 'Place', 'Date ' . $type, 'Reported by', 'Status', 'Submitted'];
                $row = fn (Report $r) => [
                    $r->id,
                    $r->item_name,
                    $r->category,
                    $r->place,
                    $r->date?->format('Y-m-d'),
                    $r->user?->name,
                    $r->status,
                    $r->created_at?->format('Y-m-d H:i'),
                ];
        }

        $query
            ->when($f['from'] ?? null, fn ($q, $d) => $q->whereDate($dateColumn, '>=', $d))
            ->when($f['to'] ?? null, fn ($q, $d) => $q->whereDate($dateColumn, '<=', $d))
            ->when($f['status'] ?? null, function ($q, $status) use ($type) {
                if (in_array($type, ['drop_offs', 'pickups'], true)) {
                    $q->where('identity_verified', $status === 'verified');
                } else {
                    $q->where('status', $status);
                }
            })
            ->orderByDesc($dateColumn)
            ->orderByDesc($query->getModel()->getTable() . '.id');

        return [
            'title'   => self::TYPES[$type],
            'headers' => $headers,
            'query'   => $query,
            'row'     => $row,
        ];
    }

    /**
     * Neutralise spreadsheet formula injection: user-typed text such as
     * "=HYPERLINK(...)" must not run when an admin opens the CSV in Excel.
     */
    private function safeCell(mixed $value): mixed
    {
        if (is_string($value) && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}