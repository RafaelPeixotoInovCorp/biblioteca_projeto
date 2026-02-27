<div>
    @if (session()->has('message'))
        <div class="alert alert-success shadow-lg mb-4">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Pesquisa -->
    <div class="mb-4">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Pesquisar livros..."
               class="input input-bordered w-full" />
    </div>

    <!-- Tabela de Livros -->
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th>Nome</th>
                <th>ISBN</th>
                <th>Editora</th>
                <th>Autores</th>
                <th>Preço</th>
                <th>Capa</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($livros as $livro)
                <tr>
                    <td class="font-medium">{{ $livro->nome }}</td>
                    <td class="font-mono">{{ $livro->isbn }}</td>
                    <td>{{ $livro->editora?->nome ?? 'N/A' }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @foreach($livro->autores as $autor)
                                <span class="badge badge-primary badge-outline">{{ $autor->nome }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>{{ number_format($livro->preco, 2, ',', '.') }} €</td>
                    <td>
                        @if($livro->imagem_capa)
                            <div class="avatar">
                                <div class="w-12 h-16 rounded">
                                    <img src="{{ Storage::url($livro->imagem_capa) }}" alt="Capa">
                                </div>
                            </div>
                        @else
                            <span class="text-base-content/50">Sem imagem</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.livros.editar', $livro->id) }}" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <button wire:click="delete({{ $livro->id }})"
                                    wire:confirm="Tem a certeza que deseja eliminar este livro?"
                                    class="btn btn-sm btn-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-base-content/50">
                        Nenhum livro encontrado
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $livros->links() }}
    </div>
</div>
