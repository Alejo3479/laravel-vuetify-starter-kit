<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
                $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy($sort, $order);
    
        $clientes = $clientesQuery->paginate($limit)->withQueryString();
        return Inertia::render('Users/Index', [
            'type' => 'cliente',
            'payload' => $clientes,
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
            'type' => 'cliente',
            'action' => 'create',
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClienteRequest $request)
    {
        try {
            $cliente  = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make($request->password),
            ]);
            if ($request->has('roles')) {
                $cliente ->assignRole($request->roles);
            }
            $message = sprintf('Cliente  "%s" registrado exitosamente.', $cliente ->name);
            Inertia::flash('toast', ['type' => 'success', 'message' => $message]);
            return redirect()->back();

        } catch (\Exception $e) {
            $message = sprintf('Hubo un error al intentar crear el cliente: %s', $e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $cliente)
    {
        $roles = Role::select(['id', 'name'])->where('name', 'Cliente')->get();
        $cliente->load('roles:id');

        return Inertia::render('Users/Form', [
            'type' => 'cliente',
            'action' => 'show',
            'payload' => [
                'id' => $cliente->id,
                'name' => $cliente->name,
                'email' => $cliente->email,
                'role_ids' => $cliente->roles->pluck('id')->toArray(),
            ],
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $cliente)
    {
        $roles = Role::select(['id', 'name'])->where('name', 'Cliente')->get();
        $cliente->load('roles:id');

        return Inertia::render('Users/Form', [
            'type' => 'cliente',
            'action' => 'edit',
            'payload' => [
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
    public function update(User $cliente, UpdateClienteRequest $request)
    {
        try {
            $data = [
                'name' => trim($request->name),
                'email' => trim($request->email),
            ];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $cliente->update($data);
            $cliente->syncRoles($request->input('roles', []));
            $message = sprintf('Cliente "%s" actualizado exitosamente.', $cliente->name);

            Inertia::flash('toast', ['type' => 'success', 'message' => $message]);
            return redirect()->back();

        } catch (\Exception $e) {
            $message = sprintf('Hubo un error al actualizar el cliente: %s', $e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => $message]);
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $cliente)
    {
       try {
            if ($cliente->id === Auth::id()) {
                Inertia::flash('toast', ['type' => 'error','message' => 'No puedes eliminar tu propia cuenta de usuario.']);
                return redirect()->back();
            }
            $userName = $cliente->name;
            $cliente->delete();
            $message = sprintf('Cliente "%s" eliminado exitosamente.', $userName);
            Inertia::flash('toast', ['type' => 'success','message' => $message]);
            return to_route('clientes.index');

        } catch (\Exception $e) {
            $message = sprintf('Hubo un error al intentar eliminar el cliente: %s', $e->getMessage());
            Inertia::flash('toast', ['type' => 'error','message' => $message]);
            return redirect()->back();
        }
    }
}
