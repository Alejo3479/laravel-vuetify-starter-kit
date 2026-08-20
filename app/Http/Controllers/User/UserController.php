<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
