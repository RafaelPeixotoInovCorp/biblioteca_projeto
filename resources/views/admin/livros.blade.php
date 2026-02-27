<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Gestão de Livros') }}
        </h2>
    </x-slot>

    @php
        $search = request()->get('search', '');
        $searchType = request()->get('type', 'tudo');

        // Construir a query com base no tipo de pesquisa
        $livrosQuery = App\Models\Livro::with(['editora', 'autores']);

        if ($search) {
            if ($searchType === 'isbn') {
                $livrosQuery->where('isbn', 'like', '%' . $search . '%');
            } elseif ($searchType === 'titulo') {
                $livrosQuery->where('nome', 'like', '%' . $search . '%');
            } elseif ($searchType === 'autor') {
                $livrosQuery->whereHas('autores', function($q) use ($search) {
                    $q->where('nome', 'like', '%' . $search . '%');
                });
            } elseif ($searchType === 'editora') {
                $livrosQuery->whereHas('editora', function($q) use ($search) {
                    $q->where('nome', 'like', '%' . $search . '%');
                });
            } else {
                // Pesquisa em tudo
                $livrosQuery->where(function($q) use ($search) {
                    $q->where('isbn', 'like', '%' . $search . '%')
                      ->orWhere('nome', 'like', '%' . $search . '%')
                      ->orWhereHas('autores', function($q) use ($search) {
                          $q->where('nome', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('editora', function($q) use ($search) {
                          $q->where('nome', 'like', '%' . $search . '%');
                      });
                });
            }
        }

        $livros = $livrosQuery->orderBy('created_at', 'desc')->paginate(10);
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Cabeçalho com título e botão -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold">Lista de Livros</h3>
                    <a href="{{ route('admin.livros.novo') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo Livro
                    </a>
                </div>

                <!-- Barra de pesquisa avançada -->
                <div class="mb-6">
                    <form method="GET" action="{{ route('admin.livros') }}" class="flex flex-col md:flex-row gap-2">
                        <div class="form-control flex-1">
                            <div class="input-group">
                                <select name="type" class="select select-bordered w-32">
                                    <option value="tudo" {{ $searchType == 'tudo' ? 'selected' : '' }}>Tudo</option>
                                    <option value="isbn" {{ $searchType == 'isbn' ? 'selected' : '' }}>ISBN</option>
                                    <option value="titulo" {{ $searchType == 'titulo' ? 'selected' : '' }}>Título</option>
                                    <option value="autor" {{ $searchType == 'autor' ? 'selected' : '' }}>Autor</option>
                                    <option value="editora" {{ $searchType == 'editora' ? 'selected' : '' }}>Editora</option>
                                </select>
                                <input type="text"
                                       name="search"
                                       value="{{ $search }}"
                                       placeholder="Pesquisar livros..."
                                       class="input input-bordered w-full" />
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.livros') }}" class="btn btn-ghost">
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
                            Resultados para
                            @if($searchType == 'isbn')
                                <span class="badge badge-primary">ISBN: {{ $search }}</span>
                            @elseif($searchType == 'titulo')
                                <span class="badge badge-primary">Título: {{ $search }}</span>
                            @elseif($searchType == 'autor')
                                <span class="badge badge-primary">Autor: {{ $search }}</span>
                            @elseif($searchType == 'editora')
                                <span class="badge badge-primary">Editora: {{ $search }}</span>
                            @else
                                <span class="badge badge-primary">"{{ $search }}"</span>
                            @endif
                            - {{ $livros->total() }} livro(s) encontrado(s)
                        </div>
                    @endif
                </div>

                <!-- Tabela de Livros -->
                <div class="overflow-x-auto">
                    <table class="table">
                        <!-- cabeçalho -->
                        <thead>
                        <tr>
                            <th>
                                <label>
                                    <input type="checkbox" class="checkbox" />
                                </label>
                            </th>
                            <th>Livro</th>
                            <th>ISBN</th>
                            <th>Editora</th>
                            <th>Autores</th>
                            <th>Preço</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($livros as $livro)
                            <tr>
                                <th>
                                    <label>
                                        <input type="checkbox" class="checkbox" />
                                    </label>
                                </th>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle h-12 w-12">
                                                @if($livro->imagem_capa)
                                                    <img src="{{ Storage::url($livro->imagem_capa) }}"
                                                         alt="{{ $livro->nome }}" />
                                                @else
                                                    <div class="bg-base-200 h-full w-full flex items-center justify-center">
                                                        <span class="text-2xl">📚</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $livro->nome }}</div>
                                            <div class="text-sm opacity-50">{{ Str::limit($livro->bibliografia ?? 'Sem descrição', 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-mono text-sm badge badge-ghost">{{ $livro->isbn }}</span>
                                </td>
                                <td>
                                    @if($livro->editora)
                                        <span class="badge badge-outline">{{ $livro->editora->nome }}</span>
                                    @else
                                        <span class="text-sm opacity-50">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1">
                                        @foreach($livro->autores as $autor)
                                            <span class="badge badge-primary badge-outline badge-sm">{{ $autor->nome }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="font-bold text-primary">{{ number_format($livro->preco, 2, ',', '.') }} €</span>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <a href="{{ route('admin.livros.editar', $livro->id) }}" class="btn btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <!-- Botão que abre o modal -->
                                        <button class="btn btn-ghost btn-xs" onclick="delete_livro_{{ $livro->id }}.showModal()" title="Eliminar">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>

                                        <!-- Modal de confirmação -->
                                        <dialog id="delete_livro_{{ $livro->id }}" class="modal modal-bottom sm:modal-middle">
                                            <div class="modal-box">
                                                <h3 class="text-lg font-bold text-error">Confirmar Eliminação</h3>
                                                <p class="py-4">Tem a certeza que deseja eliminar o livro <span class="font-bold">"{{ $livro->nome }}"</span>?</p>
                                                <p class="text-sm text-base-content/70">Esta ação não pode ser desfeita.</p>
                                                <div class="modal-action">
                                                    <form method="dialog">
                                                        <button class="btn btn-ghost">Cancelar</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.livros.eliminar', $livro->id) }}" class="inline">
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
                                <td colspan="7" class="text-center py-8">
                                    <div class="alert alert-info shadow-lg">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Nenhum livro encontrado.</span>
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
                    {{ $livros->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
