<div class="p-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-base-content">Editoras</h2>

        @if($canManage)
            <a href="{{ route('admin.editoras.novo') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar Editora
            </a>
        @endif
    </div>

    <!-- Pesquisa -->
    <div class="mb-8">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pesquisar por nome</span>
            </label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Nome da editora..."
                   class="input input-bordered w-full" />
        </div>
    </div>

    <!-- Grid de Editoras -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($editoras as $editora)
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300">
                <figure class="px-4 pt-4 flex justify-center">
                    <a href="{{ route('livros.index', ['filtro_editora' => $editora->id]) }}" class="block">
                        <div class="avatar">
                            <div class="w-32 rounded-xl">
                                @if($editora->logotipo)
                                    <img src="{{ Storage::url($editora->logotipo) }}" alt="{{ $editora->nome }}" />
                                @else
                                    <div class="bg-primary/20 w-full h-full flex items-center justify-center">
                                        <span class="text-4xl font-bold text-primary">{{ substr($editora->nome, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                </figure>

                <div class="card-body items-center text-center">
                    <a href="{{ route('livros.index', ['filtro_editora' => $editora->id]) }}" class="hover:text-primary transition-colors">
                        <h3 class="card-title text-xl">{{ $editora->nome }}</h3>
                    </a>

                    <p class="text-base-content/70">
                        {{ $editora->livros_count }} {{ $editora->livros_count == 1 ? 'livro' : 'livros' }}
                    </p>

                    <div class="card-actions justify-center mt-2">
                        <a href="{{ route('livros.index', ['filtro_editora' => $editora->id]) }}" class="btn btn-sm btn-outline btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Ver livros
                        </a>
                    </div>

                    @if($canManage)
                        <div class="card-actions justify-center mt-2 pt-2 border-t border-base-200 w-full">
                            <a href="{{ route('admin.editoras.editar', $editora->id) }}" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Editar
                            </a>
                            <button wire:click="delete({{ $editora->id }})"
                                    wire:confirm="Tem a certeza que deseja eliminar esta editora?"
                                    class="btn btn-sm btn-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="alert alert-info shadow-lg">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Nenhuma editora encontrada.</span>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginação -->
    <div class="mt-8">
        {{ $editoras->links() }}
    </div>
</div>
