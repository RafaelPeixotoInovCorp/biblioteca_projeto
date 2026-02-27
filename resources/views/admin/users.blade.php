<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Gestão de Utilizadores') }}
        </h2>
    </x-slot>

    @php
        // Obter termo de pesquisa da query string
        $search = request()->get('search', '');

        // Query de utilizadores com pesquisa
        $usersQuery = App\Models\User::with('roles');

        if ($search) {
            $usersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $usersQuery->get();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cards de estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card bg-primary text-primary-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Total de Utilizadores</h3>
                        <p class="text-4xl font-bold">{{ App\Models\User::count() }}</p>
                    </div>
                </div>

                <div class="card bg-secondary text-secondary-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Administradores</h3>
                        <p class="text-4xl font-bold">{{ App\Models\User::whereHas('roles', function($q) { $q->where('name', 'admin'); })->count() }}</p>
                    </div>
                </div>

                <div class="card bg-accent text-accent-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Clientes</h3>
                        <p class="text-4xl font-bold">{{ App\Models\User::whereDoesntHave('roles')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Cards de ações rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="card-body">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title">Adicionar Utilizador</h3>
                                <p class="text-base-content/70">Criar uma nova conta de utilizador</p>
                            </div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <button class="btn btn-primary" onclick="document.getElementById('novo-user-modal').showModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Novo Utilizador
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                    <div class="card-body">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-full bg-secondary/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title">Atribuir Roles</h3>
                                <p class="text-base-content/70">Gerir permissões dos utilizadores</p>
                            </div>
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('admin.roles') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Gerir Roles
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Barra de Pesquisa -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.users') }}" class="flex gap-2">
                    <div class="form-control flex-1">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Pesquisar por nome ou email..."
                                   class="input input-bordered w-full" />
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Pesquisar
                            </button>
                            @if($search)
                                <a href="{{ route('admin.users') }}" class="btn btn-ghost">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resultados da pesquisa -->
            @if($search)
                <div class="mb-4">
                    <p class="text-base-content/70">
                        Resultados para "{{ $search }}": {{ $users->count() }} utilizador(es) encontrado(s)
                    </p>
                </div>
            @endif

            <!-- Lista de utilizadores em cards -->
            <h3 class="text-2xl font-bold mb-4">Utilizadores</h3>
            @if($users->isEmpty())
                <div class="alert alert-info shadow-lg">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Nenhum utilizador encontrado.</span>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($users as $user)
                        <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                            <div class="card-body">
                                <div class="flex items-center gap-4">
                                    @if($user->profile_photo_url)
                                        <div class="avatar">
                                            <div class="w-16 h-16 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-primary">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <h3 class="card-title text-lg">{{ $user->name }}</h3>
                                        <p class="text-sm text-base-content/70">{{ $user->email }}</p>
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @forelse($user->roles as $role)
                                                <span class="badge badge-primary">{{ $role->name }}</span>
                                            @empty
                                                <span class="badge badge-ghost">Sem role</span>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <div class="card-actions justify-end mt-4 pt-4 border-t border-base-200">
                                    <button class="btn btn-sm btn-info" onclick="document.getElementById('edit-user-{{ $user->id }}').showModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Editar
                                    </button>
                                    <!-- Botão que abre o modal (apenas se não for o próprio utilizador) -->
                                    @if($user->id !== auth()->id())
                                        <button class="btn btn-ghost btn-xs" onclick="delete_user_{{ $user->id }}.showModal()" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <!-- Modal de confirmação -->
                                        <dialog id="delete_user_{{ $user->id }}" class="modal modal-bottom sm:modal-middle">
                                            <div class="modal-box">
                                                <h3 class="text-lg font-bold text-error">Confirmar Eliminação</h3>
                                                <p class="py-4">Tem a certeza que deseja eliminar o utilizador <span class="font-bold">"{{ $user->name }}"</span>?</p>
                                                <p class="text-sm text-base-content/70">Esta ação não pode ser desfeita.</p>
                                                <div class="modal-action">
                                                    <form method="dialog">
                                                        <button class="btn btn-ghost">Cancelar</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-error">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                            Sim, eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </dialog>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Modal Editar Utilizador -->
                        <dialog id="edit-user-{{ $user->id }}" class="modal">
                            <div class="modal-box">
                                <h3 class="font-bold text-lg">Editar Utilizador: {{ $user->name }}</h3>
                                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="mt-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-control mb-4">
                                        <label class="label">Nome</label>
                                        <input type="text" name="name" value="{{ $user->name }}" class="input input-bordered" required>
                                    </div>

                                    <div class="form-control mb-4">
                                        <label class="label">Email</label>
                                        <input type="email" name="email" value="{{ $user->email }}" class="input input-bordered" required>
                                    </div>

                                    <div class="form-control mb-4">
                                        <label class="label">Roles</label>
                                        <select name="roles[]" multiple class="select select-bordered h-32">
                                            @foreach(App\Models\Role::all() as $role)
                                                <option value="{{ $role->id }}" {{ $user->roles->contains($role) ? 'selected' : '' }}>
                                                    {{ $role->name }} - {{ $role->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label class="label-text-alt">Use Ctrl para selecionar múltiplos</label>
                                    </div>

                                    <div class="modal-action">
                                        <button type="button" class="btn" onclick="document.getElementById('edit-user-{{ $user->id }}').close()">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </dialog>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Novo Utilizador -->
    <dialog id="novo-user-modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Novo Utilizador</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4">
                @csrf

                <div class="form-control mb-4">
                    <label class="label">Nome</label>
                    <input type="text" name="name" required class="input input-bordered">
                </div>

                <div class="form-control mb-4">
                    <label class="label">Email</label>
                    <input type="email" name="email" required class="input input-bordered">
                </div>

                <div class="form-control mb-4">
                    <label class="label">Password</label>
                    <input type="password" name="password" required class="input input-bordered">
                </div>

                <div class="form-control mb-4">
                    <label class="label">Confirmar Password</label>
                    <input type="password" name="password_confirmation" required class="input input-bordered">
                </div>

                <div class="form-control mb-4">
                    <label class="label">Roles</label>
                    <select name="roles[]" multiple class="select select-bordered h-32">
                        @foreach(App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}">{{ $role->name }} - {{ $role->description }}</option>
                        @endforeach
                    </select>
                    <label class="label-text-alt">Use Ctrl para selecionar múltiplos</label>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn" onclick="document.getElementById('novo-user-modal').close()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Utilizador</button>
                </div>
            </form>
        </div>
    </dialog>
</x-app-layout>
