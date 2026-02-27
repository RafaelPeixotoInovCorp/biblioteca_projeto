<x-app-layout>
    <!-- Removido o slot "header" que mostrava "Dashboard" no topo -->

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagem de boas-vindas -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-base-content">Bem-vindo, {{ Auth::user()->name }}!</h1>
                <p class="text-base-content/70">
                    @if(auth()->user()->isAdmin())
                        Tem acesso de administrador a todas as funcionalidades.
                    @elseif(auth()->user()->hasRole('cliente'))
                        Tem acesso de visualização a todo o catálogo.
                    @else
                        Tem acesso às seguintes funcionalidades:
                    @endif
                </p>
            </div>

            <!-- Cards de acesso (visíveis para todos que podem ver) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                @if(auth()->user()->canViewBooks())
                    <div class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                        <figure class="px-10 pt-10">
                            <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </figure>
                        <div class="card-body items-center text-center">
                            <h3 class="card-title text-2xl text-base-content">Livros</h3>
                            <p class="text-base-content/70">
                                @if(auth()->user()->canManageBooks())
                                    Gerir catálogo de livros, adicionar, editar e eliminar.
                                @else
                                    Visualizar catálogo de livros disponíveis.
                                @endif
                            </p>
                            <div class="badge badge-primary mt-2">
                                {{ App\Models\Livro::count() }} livros
                            </div>
                            <div class="card-actions mt-4">
                                @if(auth()->user()->canManageBooks())
                                    <!-- Admin vai direto para a tabela de gestão -->
                                    <a href="{{ route('admin.livros') }}" class="btn btn-primary">
                                        Gerir Livros
                                    </a>
                                @else
                                    <!-- Cliente vai para a vista de cliente -->
                                    <a href="{{ route('livros.index') }}" class="btn btn-primary">
                                        Ver Livros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->canViewAuthors())
                    <div class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                        <figure class="px-10 pt-10">
                            <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </figure>
                        <div class="card-body items-center text-center">
                            <h3 class="card-title text-2xl text-base-content">Autores</h3>
                            <p class="text-base-content/70">
                                @if(auth()->user()->canManageAuthors())
                                    Gerir autores, adicionar fotos e associar a livros.
                                @else
                                    Visualizar autores e as suas obras.
                                @endif
                            </p>
                            <div class="badge badge-primary mt-2">
                                {{ App\Models\Autor::count() }} autores
                            </div>
                            <div class="card-actions mt-4">
                                @if(auth()->user()->canManageAuthors())
                                    <!-- Admin vai direto para a tabela de gestão -->
                                    <a href="{{ route('admin.autores') }}" class="btn btn-primary">
                                        Gerir Autores
                                    </a>
                                @else
                                    <a href="{{ route('autores.index') }}" class="btn btn-primary">
                                        Ver Autores
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->canViewPublishers())
                    <div class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                        <figure class="px-10 pt-10">
                            <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </figure>
                        <div class="card-body items-center text-center">
                            <h3 class="card-title text-2xl text-base-content">Editoras</h3>
                            <p class="text-base-content/70">
                                @if(auth()->user()->canManagePublishers())
                                    Gerir editoras, logótipos e relações com livros.
                                @else
                                    Visualizar editoras e os seus catálogos.
                                @endif
                            </p>
                            <div class="badge badge-primary mt-2">
                                {{ App\Models\Editora::count() }} editoras
                            </div>
                            <div class="card-actions mt-4">
                                @if(auth()->user()->canManagePublishers())
                                    <!-- Admin vai direto para a tabela de gestão -->
                                    <a href="{{ route('admin.editoras') }}" class="btn btn-primary">
                                        Gerir Editoras
                                    </a>
                                @else
                                    <a href="{{ route('editoras.index') }}" class="btn btn-primary">
                                        Ver Editoras
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Se não tem nenhuma permissão de visualização -->
            @if(!auth()->user()->hasAnyViewPermission())
                <div class="alert alert-warning shadow-lg">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>A sua conta ainda não tem permissões de acesso. Contacte o administrador.</span>
                    </div>
                </div>
            @endif

            <!-- Painel de Administração (apenas para admin) -->
            @if(auth()->user()->isAdmin())
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-base-content mb-6">Painel de Administração</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Card Gestão de Utilizadores -->
                        <div class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                            <figure class="px-10 pt-10">
                                <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </figure>
                            <div class="card-body items-center text-center">
                                <h3 class="card-title text-2xl text-base-content">Gestão de Utilizadores</h3>
                                <p class="text-base-content/70">Gerir utilizadores, criar contas e atribuir permissões.</p>
                                <div class="badge badge-primary mt-2">
                                    {{ \App\Models\User::count() }} utilizadores
                                </div>
                                <div class="card-actions mt-4">
                                    <a href="{{ route('admin.users') }}" class="btn btn-primary">Gerir Utilizadores</a>
                                </div>
                            </div>
                        </div>

                        <!-- Card Gestão de Roles -->
                        <div class="card bg-base-100 shadow-xl hover:scale-105 transition-transform duration-300">
                            <figure class="px-10 pt-10">
                                <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </figure>
                            <div class="card-body items-center text-center">
                                <h3 class="card-title text-2xl text-base-content">Gestão de Roles</h3>
                                <p class="text-base-content/70">Gerir roles e permissões do sistema.</p>
                                <div class="badge badge-primary mt-2">
                                    {{ \App\Models\Role::count() }} roles
                                </div>
                                <div class="card-actions mt-4">
                                    <a href="{{ route('admin.roles') }}" class="btn btn-primary">Gerir Roles</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
