<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Admin dashboard</h1>
            <a href="{{ route('admin.items.index') }}" class="rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a]">Manage items</a>
        </div>

        <dl class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-5">
            @foreach ($stats as $label => $value)
                <div class="rounded-xl bg-white p-5 ring-1 ring-slate-100">
                    <dt class="text-sm text-slate-500">{{ $label }}</dt>
                    <dd class="mt-1 font-display text-3xl font-bold text-[#0F2A4A]">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="mt-10 grid gap-8 lg:grid-cols-2">
            <section>
                <h2 class="font-display text-xl font-bold text-[#0F2A4A]">Recent reports</h2>
                <ul class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
                    @forelse ($recentReports as $r)
                        <li class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.items.edit', $r) }}" class="font-semibold text-slate-800 hover:underline">{{ $r->item_name }}</a>
                            <span class="text-slate-500">&middot; {{ $r->type }} &middot; {{ $r->status }} &middot; by {{ $r->user->name }}</span>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No reports yet.</li>
                    @endforelse
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-[#0F2A4A]">Recent claims</h2>
                <ul class="mt-3 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-white">
                    @forelse ($recentClaims as $c)
                        <li class="px-4 py-3 text-sm">
                            <span class="font-semibold text-slate-800">{{ $c->report->item_name }}</span>
                            <span class="text-slate-500">&middot; {{ $c->status }} &middot; by {{ $c->user->name }} &middot; {{ $c->created_at->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="px-4 py-6 text-center text-sm text-slate-500">No claims yet.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-app-layout>