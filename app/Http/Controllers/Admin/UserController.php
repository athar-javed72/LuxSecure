<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\RoleAssignmentService;
use App\Support\CsvExporter;
use App\Support\Rbac;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function __construct(private RoleAssignmentService $assignments) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->with('roles')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('email', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', $this->formData(Auth::user()));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        $this->assignments->syncRoles($request->user(), $user, $request->input('roles', []));
        $this->assignments->syncDirectPermissions($request->user(), $user, $request->input('permissions', []));

        return redirect()->route('admin.users.show', $user)->with('success', 'User created.');
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);
        $user->load(['roles', 'permissions']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);
        $user->load(['roles', 'permissions']);

        return view('admin.users.edit', array_merge($this->formData(Auth::user()), [
            'user' => $user,
            'selectedRoles' => $user->getRoleNames()->all(),
            'selectedPermissions' => $user->getDirectPermissions()->pluck('name')->all(),
        ]));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        if ($user->isSuperAdmin() && ! $request->user()->isSuperAdmin()) {
            return back()->with('error', 'Only a super admin can modify another super admin.');
        }

        $isActive = $request->boolean('is_active', true);
        if (! $isActive && $user->isLastSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate the last remaining super admin.');
        }

        $data = $request->safe()->only('name', 'email', 'phone');
        $data['is_active'] = $isActive;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->user()->can('users.assign_roles')) {
            $this->assignments->syncRoles($request->user(), $user->fresh(), $request->input('roles', []));
        }

        if ($request->user()->can('users.assign_permissions')) {
            $this->assignments->syncDirectPermissions($request->user(), $user->fresh(), $request->input('permissions', []));
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || $user->isLastSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', User::class);

        $rows = User::query()->with('roles')->orderBy('id')->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'roles' => $u->getRoleNames()->implode('|'),
            'is_active' => $u->is_active ? '1' : '0',
        ]);

        return CsvExporter::download('users.csv', ['id', 'name', 'email', 'phone', 'roles', 'is_active'], $rows);
    }

    public function bulk(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', User::class);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
            'action' => ['required', 'in:deactivate,activate,delete'],
        ]);

        $users = User::query()->whereIn('id', $data['ids'])->get();

        foreach ($users as $user) {
            if ($user->id === Auth::id() || $user->isLastSuperAdmin()) {
                continue;
            }
            if ($data['action'] === 'delete' && $request->user()->can('delete', $user)) {
                $user->delete();
            } elseif ($data['action'] === 'deactivate' && $request->user()->can('update', $user)) {
                $user->update(['is_active' => false]);
            } elseif ($data['action'] === 'activate' && $request->user()->can('update', $user)) {
                $user->update(['is_active' => true]);
            }
        }

        return back()->with('success', 'Bulk action applied to users.');
    }

    /** @return array<string, mixed> */
    private function formData(User $actor): array
    {
        return [
            'assignableRoles' => $this->assignments->assignableRolesFor($actor),
            'groupedPermissions' => Rbac::groupedPermissionsFromDb()->map(
                fn ($perms) => $perms->filter(fn ($p) => $actor->isSuperAdmin() || $actor->can($p->name))
            )->filter(fn ($group) => $group->isNotEmpty()),
        ];
    }
}
