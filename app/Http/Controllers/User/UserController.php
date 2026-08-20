<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
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
        $usersQuery = User::query()
            ->select(['id', 'name', 'email'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $order);
        $users = $usersQuery->paginate($limit)->withQueryString();
        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['q', 'sort', 'order', 'limit']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Users/Form', [
            'action' => 'create',
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make($request->password),
            ]);
            if ($request->has('roles')) {
                $user->assignRole($request->roles);
            }
            $message = sprintf('Usuario "%s" registrado exitosamente.', $user->name);
            return to_route('users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al intentar crear el usuario.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $roles = Role::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();
        $user->load('roles:id');
        
        return Inertia::render('Users/Form', [
            'action' => 'show',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_ids' => $user->roles->pluck('id')->toArray(),
            ],
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::select(['id', 'name'])
            ->orderBy('name', 'asc')
            ->get();
        $user->load('roles:id');
        return Inertia::render('Users/Form', [
            'action' => 'edit',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_ids' => $user->roles->pluck('id')->toArray(),
            ],
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(User $user, UpdateUserRequest $request)
    {
        try {
            $data = [
                'name' => trim($request->name),
                'email' => trim($request->email),
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
            $user->syncRoles($request->input('roles', []));
            $message = sprintf('Usuario "%s" actualizado exitosamente.', $user->name);
            return to_route('users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al actualizar el usuario.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if ($user->id === Auth::id()) {
                return redirect()->back()
                    ->with('error', 'No puedes eliminar tu propia cuenta de usuario.');
            }
            $userName = $user->name;
            $user->delete();
            $message = sprintf('Usuario "%s" eliminado exitosamente.', $userName);
            return to_route('users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Hubo un error al intentar eliminar el usuario.');
        }
    }
}
