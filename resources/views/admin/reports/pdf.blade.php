<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .meta { color: #64748b; margin-bottom: 12px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0f2a4a; color: #fff; text-align: left; padding: 6px; font-size: 9px; }
        td { padding: 5px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .note { margin-top: 10px; color: #b45309; }
    </style>
</head>
<body>
    <h1>Campus Find &mdash; {{ $title }}</h1>

    <div class="meta">
        Date range:
        {{ ($filters['from'] ?? null) ?: 'start' }} to {{ ($filters['to'] ?? null) ?: 'today' }}
        &nbsp;|&nbsp; Status: {{ $statusLabel ?? 'All' }}
        &nbsp;|&nbsp; Records: {{ number_format($total) }}<br>
        Generated {{ $generatedAt->format('Y-m-d H:i') }} by {{ $generatedBy }}
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}">No records match these filters.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($truncated)
        <p class="note">
            Showing the latest {{ number_format($limit) }} of {{ number_format($total) }} records.
            Narrow the date range or use the CSV export for the full set.
        </p>
    @endif
</body>
</html>