<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\Claim;
use App\Models\ItemTransaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Admin user management: list, search, filter, view details,
 * suspend / reactivate accounts and change roles.
 *
 * Route access is restricted by the 'admin' middleware; each action is also
 * checked against UserPolicy (no self-suspension, no removing the last admin).
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role'   => ['nullable', Rule::in(User::ROLES)],
            'status' => ['nullable', Rule::in(['active', 'suspended'])],
        ]);

        $users = User::query()
            ->withCount(['reports', 'claims'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                // Escape LIKE wildcards so "%" or "_" typed by an admin match literally.
                $like = '%' . addcslashes($search, '%_\\') . '%';

                $query->where(function ($q) use ($like, $search) {
                    $q->where('name', 'like', $like)
                      ->orWhere('email', 'like', $like);

                    if (ctype_digit($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
            ->when($filters['status'] ?? null, function ($q, $status) {
                $status === 'suspended'
                    ? $q->whereNotNull('suspended_at')
                    : $q->whereNull('suspended_at');
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users'   => $users,
            'filters' => $filters,
            'roles'   => User::ROLES,
        ]);
    }

    public function show(User $user)
    {
        $user->loadCount(['reports', 'claims']);

        $reports = $user->reports()->latest()->limit(10)->get();

        $claims = $user->claims()->with('report:id,item_name')->latest()->limit(10)->get();

        $pickupQuery = ItemTransaction::query()
            ->where('type', ItemTransaction::TYPE_PICKUP)
            ->whereIn('claim_id', Claim::where('user_id', $user->id)->select('id'));

        $pickupCount = (clone $pickupQuery)->count();
        $pickups = $pickupQuery->with('report:id,item_name')
            ->latest('recorded_at')
            ->limit(10)
            ->get();

        return view('admin.users.show', [
            'user'        => $user,
            'roles'       => User::ROLES,
            'reports'     => $reports,
            'claims'      => $claims,
            'pickups'     => $pickups,
            'pickupCount' => $pickupCount,
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('changeRole', $user);

        $user->role = $request->validated('role');
        $user->save();

        return back()->with('status', "{$user->name}'s role is now {$user->role}.");
    }

    public function suspend(User $user): RedirectResponse
    {
        Gate::authorize('suspend', $user);

        $user->suspended_at = now();
        $user->save();

        return back()->with('status', "{$user->name} has been suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        Gate::authorize('activate', $user);

        $user->suspended_at = null;
        $user->save();

        return back()->with('status', "{$user->name} has been reactivated.");
    }
}