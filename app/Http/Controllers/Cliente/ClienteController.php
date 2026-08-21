<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class ClienteController extends Controller
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
        $clientesQuery = User::role('Cliente')
            ->select(['id', 'name', 'email'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $order);
    
        $clientes = $clientesQuery->paginate($limit)->withQueryString();
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes,
            'filters' => $request->only(['q', 'sort', 'order', 'limit']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        $roles = Role::select(['id', 'name'])->where('name', 'Cliente')->orderBy('name', 'asc')->get();

        return Inertia::render('Users/Form', [
            'action' => 'create',
            'cliente' => true,
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
    public function edit(User $cliente)
    {
        $roles = Role::select(['id', 'name'])->where('name', 'Cliente')->get();
        $cliente->load('roles:id');

        return Inertia::render('Users/Form', [
            'action' => 'edit',
            'cliente' => true,
            'user' => [
                'id' => $cliente->id,
                'name' => $cliente->name,
                'email' => $cliente->email,
                'role_ids' => $cliente->roles->pluck('id')->toArray(),
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
