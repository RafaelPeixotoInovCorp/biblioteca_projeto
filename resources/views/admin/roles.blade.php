<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Gestão de Roles') }}
        </h2>
    </x-slot>

    @php
        $search = request()->get('search', '');

        $rolesQuery = App\Models\Role::with('permissions');

        if ($search) {
            $rolesQuery->where('name', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
        }

        $roles = $rolesQuery->orderBy('name')->paginate(10);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Cabeçalho -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Lista de Roles</h3>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Role
                    </a>
                </div>

                <!-- Barra de pesquisa -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('admin.roles') }}" class="flex gap-2">
                        <div class="form-control flex-1">
                            <div class="input-group">
                                <input type="text"
                                       name="search"
                                       value="{{ $search }}"
                                       placeholder="Pesquisar roles..."
                                       class="input input-bordered w-full" />
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.roles') }}" class="btn btn-ghost">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tabela de Roles -->
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Permissões</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td class="font-bold">{{ $role->name }}</td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($role->permissions as $permission)
                                            <span class="badge badge-primary badge-outline">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-sm text-base-content/50">Nenhuma</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        @if($role->name !== 'admin')
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                                  onsubmit="return confirm('Tem a certeza que deseja eliminar este role?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost btn-xs" title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">
                                    <div class="alert alert-info shadow-lg">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Nenhum role encontrado.</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="mt-6">
                    {{ $roles->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
