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
        <h3 class="text-2xl font-bold">Gestão de Autores</h3>
        <button wire:click="create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Novo Autor
        </button>
    </div>

    <!-- Pesquisa -->
    <div class="mb-4">
        <input type="text"
               wire:model.live.debounce.300ms="search"
               placeholder="Pesquisar autores..."
               class="input input-bordered w-full" />
    </div>

    <!-- Tabela de Autores -->
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
            <thead>
            <tr>
                <th>Foto</th>
                <th>Nome</th>
                <th>Livros</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($autores as $autor)
                <tr>
                    <td>
                        @if($autor->foto)
                            <div class="avatar">
                                <div class="w-10 h-10 rounded-full">
                                    <img src="{{ Storage::url($autor->foto) }}" alt="{{ $autor->nome }}">
                                </div>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="text-sm font-bold text-primary">{{ substr($autor->nome, 0, 1) }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $autor->nome }}</td>
                    <td>{{ $autor->livros()->count() }}</td>
                    <td>
                        <div class="flex gap-1">
                            <button wire:click="edit({{ $autor->id }})" class="btn btn-sm btn-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button wire:click="delete({{ $autor->id }})"
                                    wire:confirm="Tem a certeza que deseja eliminar este autor?"
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
                        Nenhum autor encontrado
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        {{ $autores->links() }}
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
                               placeholder="Nome do autor" />
                        @error('nome')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Foto</span>
                        </label>
                        <input type="file" wire:model="nova_foto"
                               class="file-input file-input-bordered w-full @error('nova_foto') file-input-error @enderror"
                               accept="image/*" />
                        @error('nova_foto')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror

                        @if($foto && !$nova_foto)
                            <div class="mt-2">
                                <img src="{{ Storage::url($foto) }}"
                                     class="w-16 h-16 rounded-full"
                                     alt="Foto atual">
                                <span class="text-sm text-base-content/50">Foto atual</span>
                            </div>
                        @endif

                        @if($nova_foto)
                            <div class="mt-2">
                                <img src="{{ $nova_foto->temporaryUrl() }}"
                                     class="w-16 h-16 rounded-full"
                                     alt="Nova foto">
                                <span class="text-sm text-base-content/50">Nova foto</span>
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
