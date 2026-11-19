<x-app-layout>
    <div class="p-6 lg:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h1 class="text-3xl font-extrabold text-slate-900">Users</h1>
        </div>

        @if (session('status'))
            <div class="p-3 mb-4 text-sm border rounded-lg" style="background:#ecfdf5;border-color:#a7f3d0;color:#065f46;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Search and filters --}}
        <form method="GET" action="{{ route('admin.users.index') }}"
              class="grid gap-4 p-4 mb-6 bg-white border rounded-xl border-slate-200 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
            <div>
                <label for="search" class="block mb-1 text-sm font-medium text-slate-600">Search</label>
                <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Name, email or ID" class="w-full rounded-lg border-slate-300">
                @error('search') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="role" class="block mb-1 text-sm font-medium text-slate-600">Role</label>
                <select id="role" name="role" class="w-full rounded-lg border-slate-300">
                    <option value="">All roles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(($filters['role'] ?? null) === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block mb-1 text-sm font-medium text-slate-600">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border-slate-300">
                    <option value="">All</option>
                    <option value="active" @selected(($filters['status'] ?? null) === 'active')>Active</option>
                    <option value="suspended" @selected(($filters['status'] ?? null) === 'suspended')>Suspended</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        style="height:42px;background:#0F2A4A;color:#fff;"
                        class="inline-flex items-center px-4 text-sm font-semibold rounded-lg hover:opacity-90">
                    Apply
                </button>
                <a href="{{ route('admin.users.index') }}"
                   style="height:42px;"
                   class="inline-flex items-center px-4 text-sm font-semibold bg-white border rounded-lg border-slate-300 text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>

        <p class="mb-2 text-sm text-right text-slate-500">{{ number_format($users->total()) }} user(s)</p>

        <div class="overflow-x-auto bg-white border rounded-xl border-slate-200">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b bg-slate-50 border-slate-200 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 font-semibold">ID</th>
                        <th class="px-4 py-3 font-semibold">Name</th>
                        <th class="px-4 py-3 font-semibold">Email</th>
                        <th class="px-4 py-3 font-semibold">Role</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Reports</th>
                        <th class="px-4 py-3 font-semibold">Claims</th>
                        <th class="px-4 py-3 font-semibold">Joined</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500">{{ $user->id }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $user->name }}
                                @if ($user->is(auth()->user())) <span class="text-xs text-slate-400">(you)</span> @endif
                            </td>
                            <td class="px-4 py-3 text-slate-800">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full"
                                      style="{{ $user->isAdmin() ? 'background:#0F2A4A;color:#fff;' : 'background:#e2e8f0;color:#334155;' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($user->suspended_at)
                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full" style="background:#fee2e2;color:#991b1b;">Suspended</span>
                                @else
                                    <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full" style="background:#d1fae5;color:#065f46;">Active</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-800">{{ $user->reports_count }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ $user->claims_count }}</td>
                            <td class="px-4 py-3 text-slate-800 whitespace-nowrap">{{ $user->created_at?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-semibold" style="color:#0F2A4A;">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-slate-500">No users match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>