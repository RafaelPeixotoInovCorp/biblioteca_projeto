<div class="p-6">
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

    <!-- Modal de Criar/Editar Livro -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-3xl">
                <h3 class="font-bold text-2xl mb-4 text-base-content">{{ $modalTitle }}</h3>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- ISBN -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">ISBN <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="isbn"
                                   class="input input-bordered w-full @error('isbn') input-error @enderror"
                                   placeholder="978-3-16-148410-0" />
                            @error('isbn')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nome <span class="text-error">*</span></span>
                            </label>
                            <input type="text" wire:model="nome"
                                   class="input input-bordered w-full @error('nome') input-error @enderror"
                                   placeholder="Título do livro" />
                            @error('nome')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Editora -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Editora <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="editora_id"
                                    class="select select-bordered w-full @error('editora_id') select-error @enderror">
                                <option value="">Selecione uma editora</option>
                                @foreach($editoras as $editora)
                                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                                @endforeach
                            </select>
                            @error('editora_id')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Preço -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Preço (€) <span class="text-error">*</span></span>
                            </label>
                            <input type="number" step="0.01" wire:model="preco"
                                   class="input input-bordered w-full @error('preco') input-error @enderror"
                                   placeholder="0.00" />
                            @error('preco')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Autores -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Autores <span class="text-error">*</span></span>
                            </label>
                            <select wire:model="autores" multiple size="4"
                                    class="select select-bordered w-full @error('autores') select-error @enderror">
                                @foreach($autores as $autor)
                                    <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                                @endforeach
                            </select>
                            <label class="label">
                                <span class="label-text-alt">Use Ctrl/Cmd para selecionar múltiplos</span>
                            </label>
                            @error('autores')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Bibliografia -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Bibliografia</span>
                            </label>
                            <textarea wire:model="bibliografia" rows="3"
                                      class="textarea textarea-bordered w-full @error('bibliografia') textarea-error @enderror"
                                      placeholder="Descrição, sinopse, notas..."></textarea>
                            @error('bibliografia')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Imagem -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Imagem da Capa</span>
                            </label>
                            <input type="file" wire:model="nova_imagem"
                                   class="file-input file-input-bordered w-full @error('nova_imagem') file-input-error @enderror"
                                   accept="image/*" />
                            @error('nova_imagem')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            @if($imagem_capa && !$nova_imagem)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($imagem_capa) }}"
                                         class="w-20 h-24 object-cover rounded"
                                         alt="Capa atual">
                                    <span class="text-sm text-base-content/50">Imagem atual</span>
                                </div>
                            @endif

                            @if($nova_imagem)
                                <div class="mt-2">
                                    <img src="{{ $nova_imagem->temporaryUrl() }}"
                                         class="w-20 h-24 object-cover rounded"
                                         alt="Nova imagem">
                                    <span class="text-sm text-base-content/50">Nova imagem</span>
                                </div>
                            @endif
                        </div>
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
