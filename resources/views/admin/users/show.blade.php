<x-app-layout>
    <div class="p-6 lg:p-8">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold" style="color:#0F2A4A;">&larr; Back to users</a>

        @if (session('status'))
            <div class="p-3 mt-4 text-sm border rounded-lg" style="background:#ecfdf5;border-color:#a7f3d0;color:#065f46;">
                {{ session('status') }}
            </div>
        @endif

        @error('role')
            <div class="p-3 mt-4 text-sm border rounded-lg" style="background:#fef2f2;border-color:#fecaca;color:#991b1b;">
                {{ $message }}
            </div>
        @enderror

        {{-- Account details --}}
        <div class="p-6 mt-4 bg-white border rounded-xl border-slate-200">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900">
                        {{ $user->name }}
                        @if ($user->is(auth()->user())) <span class="text-base font-medium text-slate-400">(you)</span> @endif
                    </h1>
                    <p class="mt-1 text-slate-600">{{ $user->email }}</p>

                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full"
                              style="{{ $user->isAdmin() ? 'background:#0F2A4A;color:#fff;' : 'background:#e2e8f0;color:#334155;' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if ($user->suspended_at)
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full" style="background:#fee2e2;color:#991b1b;">
                                Suspended {{ $user->suspended_at->format('Y-m-d H:i') }}
                            </span>
                        @else
                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full" style="background:#d1fae5;color:#065f46;">Active</span>
                        @endif
                    </div>
                </div>

                <dl class="grid grid-cols-2 text-sm gap-x-8 gap-y-1">
                    <dt class="text-slate-500">User ID</dt><dd class="font-medium text-slate-900">{{ $user->id }}</dd>
                    <dt class="text-slate-500">Joined</dt><dd class="font-medium text-slate-900">{{ $user->created_at?->format('Y-m-d') }}</dd>
                    <dt class="text-slate-500">Reports</dt><dd class="font-medium text-slate-900">{{ $user->reports_count }}</dd>
                    <dt class="text-slate-500">Claims</dt><dd class="font-medium text-slate-900">{{ $user->claims_count }}</dd>
                    <dt class="text-slate-500">Pickups</dt><dd class="font-medium text-slate-900">{{ $pickupCount }}</dd>
                </dl>
            </div>

            {{-- Account actions --}}
            <div class="flex flex-wrap items-end gap-6 pt-5 mt-5 border-t border-slate-200">
                @can('changeRole', $user)
                    <form method="POST" action="{{ route('admin.users.role', $user) }}" class="flex items-end gap-2">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="role" class="block mb-1 text-sm font-medium text-slate-600">Role</label>
                            <select id="role" name="role" class="rounded-lg border-slate-300">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                                style="height:42px;background:#0F2A4A;color:#fff;"
                                class="inline-flex items-center px-4 text-sm font-semibold rounded-lg hover:opacity-90"
                                onclick="return confirm('Change this user\'s role?')">
                            Change role
                        </button>
                    </form>
                @endcan

                @if ($user->suspended_at)
                    @can('activate', $user)
                        <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                            @csrf
                            <button type="submit"
                                    style="height:42px;background:#047857;color:#fff;"
                                    class="inline-flex items-center px-4 text-sm font-semibold rounded-lg hover:opacity-90">
                                Reactivate account
                            </button>
                        </form>
                    @endcan
                @else
                    @can('suspend', $user)
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                            @csrf
                            <button type="submit"
                                    style="height:42px;background:#b91c1c;color:#fff;"
                                    class="inline-flex items-center px-4 text-sm font-semibold rounded-lg hover:opacity-90"
                                    onclick="return confirm('Suspend this account? They will be signed out immediately.')">
                                Suspend account
                            </button>
                        </form>
                    @endcan
                @endif

                @if ($user->is(auth()->user()))
                    <p class="text-sm text-slate-500">You can't suspend or change the role of your own account.</p>
                @elseif ($user->isAdmin() && ! $user->suspended_at && auth()->user()->cannot('suspend', $user))
                    <p class="text-sm text-slate-500">This is the last active admin, so it can't be suspended or demoted.</p>
                @endif
            </div>
        </div>

        {{-- Recent reports --}}
        <h2 class="mt-8 mb-2 text-xl font-bold text-slate-900">Recent reports</h2>
        <div class="overflow-x-auto bg-white border rounded-xl border-slate-200">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b bg-slate-50 border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Type</th>
                        <th class="px-4 py-3 font-semibold">Item</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $report->id }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ ucfirst($report->type) }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $report->item_name }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $report->status }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $report->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No reports yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent claims --}}
        <h2 class="mt-8 mb-2 text-xl font-bold text-slate-900">Recent claims</h2>
        <div class="overflow-x-auto bg-white border rounded-xl border-slate-200">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b bg-slate-50 border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Item</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($claims as $claim)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $claim->id }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $claim->report?->item_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $claim->status }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $claim->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No claims yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Recent pickups --}}
        <h2 class="mt-8 mb-2 text-xl font-bold text-slate-900">Recent pickups</h2>
        <div class="overflow-x-auto bg-white border rounded-xl border-slate-200">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b bg-slate-50 border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Item</th>
                        <th class="px-4 py-3 font-semibold">Identity verified</th>
                        <th class="px-4 py-3 font-semibold">Recorded at</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pickups as $pickup)
                        <tr>
                            <td class="px-4 py-3 text-slate-500">{{ $pickup->id }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $pickup->report?->item_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $pickup->identity_verified ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $pickup->recorded_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No pickups yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>