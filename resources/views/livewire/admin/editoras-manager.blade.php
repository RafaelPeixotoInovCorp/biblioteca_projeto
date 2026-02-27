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

    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-2xl font-bold">Gestão de Editoras</h3>
        <button wire:click="create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Editora
        </button>
    </div>

    <!-- Pesquisa -->
    <div class="mb-4">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Pesquisar editoras..."
               class="input input-bordered w-full" />
    </div>

    <!-- Tabela de Editoras -->
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th>Logótipo</th>
                <th>Nome</th>
                <th>Livros</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($editoras as $editora)
                <tr>
                    <td>
                        @if($editora->logotipo)
                            <div class="avatar">
                                <div class="w-10 h-10 rounded">
                                    <img src="{{ Storage::url($editora->logotipo) }}" alt="{{ $editora->nome }}">
                                </div>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded bg-primary/20 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary">{{ substr($editora->nome, 0, 1) }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $editora->nome }}</td>
                    <td>{{ $editora->livros_count }}</td>
                    <td>
                        <div class="flex gap-1">
                            <button wire:click="edit({{ $editora->id }})" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $editora->id }})"
                                    wire:confirm="Tem a certeza que deseja eliminar esta editora?"
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
                    <td colspan="4" class="text-center py-8 text-base-content/50">
                        Nenhuma editora encontrada
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $editoras->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-2xl mb-4">{{ $modalTitle }}</h3>

                <form wire:submit.prevent="save">
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Nome <span class="text-error">*</span></span>
                        </label>
                        <input type="text" wire:model="nome"
                               class="input input-bordered w-full @error('nome') input-error @enderror"
                               placeholder="Nome da editora" />
                        @error('nome')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Logótipo</span>
                        </label>
                        <input type="file" wire:model="novo_logotipo"
                               class="file-input file-input-bordered w-full @error('novo_logotipo') file-input-error @enderror"
                               accept="image/*" />
                        @error('novo_logotipo')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror

                        @if($logotipo && !$novo_logotipo)
                            <div class="mt-2">
                                <img src="{{ Storage::url($logotipo) }}"
                                     class="w-16 h-16 object-cover rounded"
                                     alt="Logótipo atual">
                                <span class="text-sm text-base-content/50">Logótipo atual</span>
                            </div>
                        @endif

                        @if($novo_logotipo)
                            <div class="mt-2">
                                <img src="{{ $novo_logotipo->temporaryUrl() }}"
                                     class="w-16 h-16 object-cover rounded"
                                     alt="Novo logótipo">
                                <span class="text-sm text-base-content/50">Novo logótipo</span>
                            </div>
                        @endif
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="$set('showModal', false)"></div>
        </div>
    @endif
</div>
