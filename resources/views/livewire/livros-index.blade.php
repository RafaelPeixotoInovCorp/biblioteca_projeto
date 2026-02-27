<div class="p-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-base-content">Livros</h2>

        @if($canManage)
            <a href="{{ route('admin.livros.novo') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar Livro
            </a>
        @endif
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <!-- Pesquisa por título -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pesquisar por título</span>
            </label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Título do livro..."
                   class="input input-bordered w-full" />
        </div>

        <!-- Filtro por autor -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por autor</span>
            </label>
            <select wire:model.live="filtro_autor" class="select select-bordered w-full">
                <option value="">Todos os autores</option>
                @foreach($autores as $autor)
                    <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filtro por editora -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por editora</span>
            </label>
            <select wire:model.live="filtro_editora" class="select select-bordered w-full">
                <option value="">Todas as editoras</option>
                @foreach($editoras as $editora)
                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Barra de ordenação -->
    <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-base-content/70">
            {{ $livros->total() }} livros encontrados
        </div>

        <div class="flex items-center gap-2">
            <span class="text-sm text-base-content/70">Ordenar por:</span>
            <div class="join">
                <button wire:click="sortBy('nome')"
                        class="join-item btn btn-sm {{ $sortField === 'nome' ? 'btn-primary' : 'btn-ghost' }}">
                    Nome
                    @if($sortField === 'nome')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                <button wire:click="sortBy('autor')"
                        class="join-item btn btn-sm {{ $sortField === 'autor' ? 'btn-primary' : 'btn-ghost' }}">
                    Autor
                    @if($sortField === 'autor')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
                <button wire:click="sortBy('preco')"
                        class="join-item btn btn-sm {{ $sortField === 'preco' ? 'btn-primary' : 'btn-ghost' }}">
                    Preço
                    @if($sortField === 'preco')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Grid de Livros -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($livros as $livro)
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                <!-- Imagem do livro com link -->
                <figure class="px-4 pt-4">
                    <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}">
                        @if($livro->imagem_capa)
                            <img src="{{ Storage::url($livro->imagem_capa) }}"
                                 alt="{{ $livro->nome }}"
                                 class="rounded-xl h-48 w-full object-cover hover:opacity-90 transition-opacity">
                        @else
                            <div class="bg-base-200 rounded-xl h-48 w-full flex items-center justify-center hover:bg-base-300 transition-colors">
                                <span class="text-base-content/30 text-8xl">📚</span>
                            </div>
                        @endif
                    </a>
                </figure>

                <div class="card-body">
                    <!-- Título com link -->
                    <h2 class="card-title text-2xl font-bold">
                        <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}"
                           class="hover:text-primary transition-colors">
                            {{ $livro->nome }}
                        </a>
                    </h2>

                    @if($livro->autores->isNotEmpty())
                        <p class="text-base-content/70 -mt-2">
                            de {{ $livro->autores->pluck('nome')->implode(', ') }}
                        </p>
                    @endif

                    <!-- Preço -->
                    @if($livro->preco)
                        <div class="mt-2">
                            <span class="text-2xl font-bold text-primary">{{ number_format($livro->preco, 2, ',', '.') }}€</span>
                        </div>
                    @endif

                    <!-- Ações -->
                    <div class="card-actions justify-end mt-4 pt-4 border-t border-base-200">
                        @if($canManage)
                            <!-- Ações para admin -->
                            <a href="{{ route('admin.livros.editar', $livro->id) }}" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Editar
                            </a>
                            <button wire:click="delete({{ $livro->id }})"
                                    wire:confirm="Tem a certeza que deseja eliminar este livro?"
                                    class="btn btn-sm btn-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        @else
                            <!-- Botão Ver Detalhes para clientes -->
                            <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}"
                               class="btn btn-primary btn-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ver detalhes
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="alert alert-info shadow-lg">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Nenhum livro encontrado.</span>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="mt-8">
        {{ $livros->links() }}
    </div>
</div>
