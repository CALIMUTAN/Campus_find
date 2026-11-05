@php $input = 'rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#0F2A4A] focus:ring-[#0F2A4A]'; @endphp
<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Item management</h1>

        @if (session('admin_status'))
            <div role="status" class="mt-5 rounded-xl border border-[#F5B301] bg-[#FFF8E1] px-4 py-3 text-sm text-[#0F2A4A]">{{ session('admin_status') }}</div>
        @endif

        <form method="GET" class="mt-6 flex flex-wrap gap-3">
            <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search name, place, description" class="{{ $input }} min-w-[14rem] flex-1">
            <select name="type" class="{{ $input }}">
                <option value="">All types</option>
                @foreach (['lost', 'found'] as $t)
                    <option value="{{ $t }}" @selected($filters['type'] === $t)>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            <select name="status" class="{{ $input }}">
                <option value="">All statuses</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected($filters['status'] === $s)>{{ $s }}</option>
                @endforeach
            </select>
            <select name="category" class="{{ $input }}">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected($filters['category'] === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-[#0F2A4A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#16396a]">Filter</button>
        </form>

        <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Place</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->item_name }}<span class="block text-xs font-normal text-slate-500">{{ $item->category }} &middot; {{ $item->user->name }}</span></td>
                            <td class="px-4 py-3">{{ ucfirst($item->type) }}</td>
                            <td class="px-4 py-3">{{ $item->place }}</td>
                            <td class="px-4 py-3">{{ $item->date->format('M j, Y') }}</td>
                            <td class="px-4 py-3">{{ $item->status }}</td>
                            <td class="px-4 py-3 text-right"><a href="{{ route('admin.items.edit', $item) }}" class="font-semibold text-[#0F2A4A] underline decoration-[#F5B301] decoration-2 underline-offset-4">Edit</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No items match your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    </div>
</x-app-layout>