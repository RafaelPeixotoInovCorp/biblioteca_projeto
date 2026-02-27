<div class="p-6">
    <!-- Mensagem de sucesso -->
    @if (session()->has('message'))
        <div class="alert alert-success mb-4 shadow-lg">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Livros</h2>
        <div class="flex gap-2">
            <button wire:click="export" class="btn btn-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exportar Excel
            </button>
            <button wire:click="create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Livro
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pesquisar</span>
            </label>
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="ISBN ou Nome..."
                   class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por Editora</span>
            </label>
            <select wire:model.live="filtro_editora" class="select select-bordered w-full">
                <option value="">Todas as Editoras</option>
                @foreach($editoras as $editora)
                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por Autor</span>
            </label>
            <select wire:model.live="filtro_autor" class="select select-bordered w-full">
                <option value="">Todos os Autores</option>
                @foreach($autores as $autor)
                    <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabela -->
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('isbn')">
                    ISBN
                    @if($sortField === 'isbn')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('nome')">
                    Nome
                    @if($sortField === 'nome')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
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
                    <td>{{ $livro->isbn }}</td>
                    <td>{{ $livro->nome }}</td>
                    <td>{{ $livro->editora?->nome ?? 'N/A' }}</td>
                    <td>
                        <div class="flex flex-wrap gap-1">
                            @foreach($livro->autores as $autor)
                                <span class="badge badge-ghost">{{ $autor->nome }}</span>
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
                            <button wire:click="edit({{ $livro->id }})" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
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
    <div class="mt-6">
        {{ $livros->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-3xl">
                <h3 class="font-bold text-lg mb-4">{{ $modalTitle }}</h3>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- ISBN -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">ISBN <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="isbn"
                                   class="input input-bordered @error('isbn') input-error @enderror" />
                            @error('isbn') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nome <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="nome"
                                   class="input input-bordered @error('nome') input-error @enderror" />
                            @error('nome') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Editora -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Editora <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="editora_id"
                                    class="select select-bordered @error('editora_id') select-error @enderror">
                                <option value="">Selecione uma editora</option>
                                @foreach($editoras as $editora)
                                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                                @endforeach
                            </select>
                            @error('editora_id') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Preço -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Preço (€) <span class="text-error">*</span></span>
                            </label>
                            <input type="number" step="0.01" wire:model="preco"
                                   class="input input-bordered @error('preco') input-error @enderror" />
                            @error('preco') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Autores -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text">Autores <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="autores" multiple size="4"
                                    class="select select-bordered @error('autores') select-error @enderror">
                                @foreach($autores as $autor)
                                    <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                                @endforeach
                            </select>
                            <label class="label">
                                <span class="label-text-alt">Use Ctrl para selecionar múltiplos</span>
                            </label>
                            @error('autores') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Bibliografia -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text">Bibliografia</span>
                            </label>
                            <textarea wire:model="bibliografia" rows="3"
                                      class="textarea textarea-bordered @error('bibliografia') textarea-error @enderror"></textarea>
                            @error('bibliografia') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Imagem -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text">Imagem da Capa</span>
                            </label>
                            <input type="file" wire:model="nova_imagem"
                                   class="file-input file-input-bordered @error('nova_imagem') file-input-error @enderror"
                                   accept="image/*" />
                            @error('nova_imagem') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror

                            @if($imagem_capa && !$nova_imagem)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($imagem_capa) }}"
                                         class="w-20 h-24 object-cover rounded"
                                         alt="Capa atual">
                                    <span class="text-sm text-base-content/50 block">Imagem atual</span>
                                </div>
                            @endif

                            @if($nova_imagem)
                                <div class="mt-2">
                                    <img src="{{ $nova_imagem->temporaryUrl() }}"
                                         class="w-20 h-24 object-cover rounded"
                                         alt="Nova imagem">
                                    <span class="text-sm text-base-content/50 block">Nova imagem</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="$set('showModal', false)" class="btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="$set('showModal', false)"></div>
        </div>
    @endif
</div>
