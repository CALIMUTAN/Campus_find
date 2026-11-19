<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h1 class="text-3xl font-extrabold text-slate-900">Reports</h1>

            <div class="flex gap-2">
                <a href="{{ route('admin.reports.csv', request()->query()) }}"
                   style="height:42px;"
                   class="inline-flex items-center px-4 rounded-lg border border-slate-300 bg-white text-sm font-semibold text-slate-800 hover:bg-slate-50">
                    Export CSV
                </a>
                <a href="{{ route('admin.reports.pdf', request()->query()) }}"
                   style="height:42px;background:#0F2A4A;color:#fff;"
                   class="inline-flex items-center px-4 rounded-lg text-sm font-semibold hover:opacity-90">
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.reports.index') }}"
              class="grid gap-4 p-4 mb-6 bg-white border rounded-xl border-slate-200 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
            <div>
                <label for="type" class="block mb-1 text-sm font-medium text-slate-600">Report</label>
                <select id="type" name="type" class="w-full rounded-lg border-slate-300" onchange="this.form.status.value=''; this.form.submit()">
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block mb-1 text-sm font-medium text-slate-600">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border-slate-300">
                    <option value="">All</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="from" class="block mb-1 text-sm font-medium text-slate-600">From</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300">
                @error('from') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="to" class="block mb-1 text-sm font-medium text-slate-600">To</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300">
                @error('to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        style="height:42px;background:#0F2A4A;color:#fff;"
                        class="inline-flex items-center px-4 text-sm font-semibold rounded-lg hover:opacity-90">
                    Apply
                </button>
                <a href="{{ route('admin.reports.index', ['type' => $filters['type']]) }}"
                   style="height:42px;"
                   class="inline-flex items-center px-4 text-sm font-semibold bg-white border rounded-lg border-slate-300 text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        {{-- Results --}}
        <div class="flex items-baseline justify-between mb-2">
            <h2 class="text-xl font-bold text-slate-900">{{ $title }}</h2>
            <p class="text-sm text-slate-500">{{ number_format($paginator->total()) }} record(s)</p>
        </div>

        <div class="overflow-x-auto bg-white border rounded-xl border-slate-200">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b bg-slate-50 border-slate-200 text-slate-600">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rows as $row)
                        <tr class="hover:bg-slate-50">
                            @foreach ($row as $cell)
                                <td class="px-4 py-3 text-slate-800">{{ $cell ?? '—' }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="px-4 py-10 text-center text-slate-500">
                                No records match these filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $paginator->links() }}
        </div>
    </div>
</x-app-layout>