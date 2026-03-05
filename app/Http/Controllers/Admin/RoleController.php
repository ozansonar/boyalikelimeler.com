<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleStoreRequest;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Http\Requests\Admin\RoleAssignRequest;
use App\Http\Requests\Admin\RolePermissionUpdateRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles'           => $this->roleService->allRoles(),
            'stats'           => $this->roleService->getStats(),
            'permissionGroups'=> $this->roleService->getPermissionsByGroup(),
            'distribution'    => $this->roleService->getRoleDistribution(),
            'users'           => $this->roleService->getUsersForAssign(),
        ]);
    }

    public function store(RoleStoreRequest $request): RedirectResponse
    {
        $this->roleService->create($request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->update($role, $request->validated());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol başarıyla güncellendi.');
    }

    public function updatePermissions(RolePermissionUpdateRequest $request, Role $role): JsonResponse
    {
        $this->roleService->updatePermissions($role, $request->validated()['permissions'] ?? []);

        return response()->json(['message' => 'İzinler başarıyla güncellendi.']);
    }

    public function assignRole(RoleAssignRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::findOrFail($data['user_id']);
        $role = Role::findOrFail($data['role_id']);

        $this->roleService->assignRoleToUser($user, $role);

        return redirect()->route('admin.roles.index')
            ->with('success', "{$user->name} kullanıcısına {$role->name} rolü atandı.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'Bu role atanmış kullanıcılar var. Önce kullanıcıları başka bir role taşıyın.');
        }

        $this->roleService->delete($role);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol başarıyla silindi.');
    }
}
