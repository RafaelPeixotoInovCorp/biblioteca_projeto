<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Buscar todos os roles
        $allRoles = Role::with('permissions')->get();

        // Filtrar por pesquisa
        if ($search) {
            $searchLower = strtolower($search);
            $allRoles = $allRoles->filter(function($role) use ($searchLower) {
                return str_contains(strtolower($role->name), $searchLower) ||
                    str_contains(strtolower($role->description ?? ''), $searchLower);
            });
        }

        // Ordenar por nome
        $allRoles = $allRoles->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE)->values();

        // Paginar manualmente
        $page = $request->get('page', 1);
        $perPage = 10;
        $roles = new LengthAwarePaginator(
            $allRoles->forPage($page, $perPage),
            $allRoles->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.roles', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles-form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('admin.roles')->with('success', 'Role criado com sucesso!');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles-form', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.roles')->with('success', 'Role atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'admin') {
            return redirect()->route('admin.roles')->with('error', 'Não pode eliminar o role admin!');
        }

        $role->delete();

        return redirect()->route('admin.roles')->with('success', 'Role eliminado com sucesso!');
    }
}
