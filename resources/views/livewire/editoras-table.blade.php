<div class="p-6">
    @if (session()->has('message'))
        <div class="alert alert-success mb-4">
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Editoras</h2>
        <button wire:click="create" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nova Editora
        </button>
    </div>

    <div class="mb-6">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pesquisar</span>
            </label>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nome da editora..." class="input input-bordered" />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
            <tr>
                <th wire:click="sortBy('nome')" class="cursor-pointer hover:bg-base-200">
                    Nome
                    @if($sortField === 'nome')
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </th>
                <th>Logótipo</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @forelse($editoras as $editora)
                <tr>
                    <td>{{ $editora->nome }}</td>
                    <td>
                        @if($editora->logotipo)
                            <img src="{{ Storage::url($editora->logotipo) }}" class="w-12 h-12 object-contain" alt="Logótipo">
                        @else
                            <span class="text-gray-400">Sem logótipo</span>
                        @endif
                    </td>
                    <td>
                        <button wire:click="edit({{ $editora->id }})" class="btn btn-sm btn-info mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                        <button wire:click="delete({{ $editora->id }})" wire:confirm="Tem a certeza que deseja eliminar esta editora?" class="btn btn-sm btn-error">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-4">Nenhuma editora encontrada</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $editoras->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-base-100 rounded-lg w-1/2">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-4">{{ $modalTitle }}</h3>

                    <form wire:submit.prevent="save">
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Nome <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="nome" class="input input-bordered @error('nome') input-error @enderror" />
                            @error('nome') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Logótipo</span>
                            </label>
                            <input type="file" wire:model="novo_logotipo" class="file-input file-input-bordered @error('novo_logotipo') file-input-error @enderror" accept="image/*" />
                            @error('novo_logotipo') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror

                            @if($logotipo && !$novo_logotipo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($logotipo) }}" class="w-16 h-16 object-contain" alt="Logótipo atual">
                                    <span class="text-sm text-gray-500">Logótipo atual</span>
                                </div>
                            @endif

                            @if($novo_logotipo)
                                <div class="mt-2">
                                    <img src="{{ $novo_logotipo->temporaryUrl() }}" class="w-16 h-16 object-contain" alt="Novo logótipo">
                                    <span class="text-sm text-gray-500">Novo logótipo</span>
                                </div>
                            @endif
                        </div>

                        <div class="modal-action">
                            <button type="button" wire:click="$set('showModal', false)" class="btn">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
