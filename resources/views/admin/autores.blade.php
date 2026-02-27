<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Gestão de Autores') }}
        </h2>
    </x-slot>

    @php
        $search = request()->get('search', '');

        $autoresQuery = App\Models\Autor::withCount('livros');

        if ($search) {
            $autoresQuery->where('nome', 'like', '%' . $search . '%');
        }

        $autores = $autoresQuery->orderBy('nome')->paginate(10);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Cabeçalho -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Lista de Autores</h3>
                    <a href="{{ route('admin.autores.novo') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Autor
                    </a>
                </div>

                <!-- Barra de pesquisa -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('admin.autores') }}" class="flex gap-2">
                        <div class="form-control flex-1">
                            <div class="input-group">
                                <input type="text"
                                       name="search"
                                       value="{{ $search }}"
                                       placeholder="Pesquisar autores por nome..."
                                       class="input input-bordered w-full" />
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.autores') }}" class="btn btn-ghost">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if($search)
                        <div class="mt-2 text-sm text-base-content/70">
                            Resultados para: <span class="badge badge-primary">"{{ $search }}"</span> - {{ $autores->total() }} autor(es) encontrado(s)
                        </div>
                    @endif
                </div>

                <!-- Tabela de Autores -->
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <th>Autor</th>
                            <th>Foto</th>
                            <th>Livros</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($autores as $autor)
                            <tr>
                                <th>
                                    <label>
                                        <input type="checkbox" class="checkbox" />
                                    </label>
                                </th>
                                <td>
                                    <div class="font-bold">{{ $autor->nome }}</div>
                                </td>
                                <td>
                                    <div class="avatar">
                                        <div class="w-12 rounded-xl">
                                            @if($autor->foto)
                                                <img src="{{ Storage::url($autor->foto) }}" alt="{{ $autor->nome }}" />
                                            @else
                                                <div class="bg-primary/20 w-full h-full flex items-center justify-center">
                                                    <span class="text-lg font-bold text-primary">{{ substr($autor->nome, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $autor->livros_count }}</span>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <!-- Botão de editar (agora é um link para a página de edição) -->
                                        <a href="{{ route('admin.autores.editar', $autor->id) }}" class="btn btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>

                                        <!-- Botão que abre o modal -->
                                        <button class="btn btn-ghost btn-xs" onclick="delete_autor_{{ $autor->id }}.showModal()" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <!-- Modal de confirmação -->
                                        <dialog id="delete_autor_{{ $autor->id }}" class="modal modal-bottom sm:modal-middle">
                                            <div class="modal-box">
                                                <h3 class="text-lg font-bold text-error">Confirmar Eliminação</h3>
                                                <p class="py-4">Tem a certeza que deseja eliminar o autor <span class="font-bold">"{{ $autor->nome }}"</span>?</p>
                                                <p class="text-sm text-base-content/70">Esta ação não pode ser desfeita.</p>
                                                <div class="modal-action">
                                                    <form method="dialog">
                                                        <button class="btn btn-ghost">Cancelar</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.autores.destroy', $autor->id) }}" class="inline">
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
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8">
                                    <div class="alert alert-info shadow-lg">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Nenhum autor encontrado.</span>
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
                    {{ $autores->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
