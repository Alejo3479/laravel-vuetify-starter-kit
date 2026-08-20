<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'name') ?: 'name';
        $order = $request->input('order', 'asc') ?: 'asc';
        $limit = $request->input('limit', 10);
        $search = $request->input('q');

        $rolesQuery = Role::query()
            ->select(['id', 'name'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $order);

        $roles = $rolesQuery->paginate($limit)->withQueryString();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['q', 'sort', 'order', 'limit']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Roles/Form', [
            'action' => 'create',
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
         try {

            $role = Role::create(['name' => trim($request->name)]);
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            $message = sprintf('Rol "%s" creado exitosamente.', $role->name);
            return to_route('roles.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al intentar crear el rol.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $permissions = Permission::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // cargar ids de los permisos que el rol ya tiene asignados
        $role->load('permissions:id');

        return Inertia::render('Roles/Index', [
            'action' => 'show',
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // cargar ids de los permisos que el rol ya tiene asignados
        $role->load('permissions:id');

        return Inertia::render('Roles/Form', [
            'action' => 'edit',
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Role $role, UpdateRoleRequest $request)
    {
        try {
           

            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);

            return to_route('roles.index')
                ->with('success', 'Rol actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al actualizar el rol.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Role $role)
    {
        try {
            // Spatie guarda los usuarios asignados en la relación 'users'
            if ($role->users()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el rol porque tiene usuarios asignados.');
            }

            $role->delete();

            return to_route('roles.index')
                ->with('success', 'Rol eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al intentar eliminar el rol.');
        }
    }
}
