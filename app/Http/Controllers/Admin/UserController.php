<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Buscar todos os utilizadores
        $allUsers = User::with('roles')->get();

        // Filtrar por pesquisa
        if ($search) {
            $searchLower = strtolower($search);
            $allUsers = $allUsers->filter(function($user) use ($searchLower) {
                return str_contains(strtolower($user->name), $searchLower) ||
                    str_contains(strtolower($user->email), $searchLower);
            });
        }

        // Ordenar por nome
        $allUsers = $allUsers->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE)->values();

        // Paginar manualmente
        $page = $request->get('page', 1);
        $perPage = 10;
        $users = new LengthAwarePaginator(
            $allUsers->forPage($page, $perPage),
            $allUsers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.users', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users-form', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        return redirect()->route('admin.users')->with('success', 'Utilizador criado com sucesso!');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $userRoles = $user->roles->pluck('id')->toArray();

        return view('admin.users-form', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('admin.users')->with('success', 'Utilizador atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Não permitir eliminar o próprio utilizador
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Não pode eliminar o próprio utilizador!');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Utilizador eliminado com sucesso!');
    }
}
