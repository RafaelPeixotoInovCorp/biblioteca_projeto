<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ isset($livro) ? 'Editar Livro' : 'Novo Livro' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ isset($livro) ? route('admin.livros.update', $livro->id) : route('admin.livros.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($livro))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-2 gap-6">
                        <!-- ISBN -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">ISBN <span class="text-error">*</span></span>
                            </label>
                            <input type="text"
                                   name="isbn"
                                   value="{{ old('isbn', $livro->isbn ?? '') }}"
                                   class="input input-bordered w-full @error('isbn') input-error @enderror"
                                   placeholder="978-3-16-148410-0"
                                   required />
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
                            <input type="text"
                                   name="nome"
                                   value="{{ old('nome', $livro->nome ?? '') }}"
                                   class="input input-bordered w-full @error('nome') input-error @enderror"
                                   placeholder="Título do livro"
                                   required />
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
                            <select name="editora_id"
                                    class="select select-bordered w-full @error('editora_id') select-error @enderror"
                                    required>
                                <option value="">Selecione uma editora</option>
                                @foreach($editoras ?? [] as $editora)
                                    <option value="{{ $editora->id }}"
                                        {{ old('editora_id', $livro->editora_id ?? '') == $editora->id ? 'selected' : '' }}>
                                        {{ $editora->nome }}
                                    </option>
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
                            <input type="number"
                                   step="0.01"
                                   name="preco"
                                   value="{{ old('preco', $livro->preco ?? '') }}"
                                   class="input input-bordered w-full @error('preco') input-error @enderror"
                                   placeholder="0.00"
                                   required />
                            @error('preco')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Autores - AGORA IGUAL ÀS EDITORAS (select simples) -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Autor <span class="text-error">*</span></span>
                            </label>
                            <select name="autor_id"
                                    class="select select-bordered w-full @error('autor_id') select-error @enderror"
                                    required>
                                <option value="">Selecione um autor</option>
                                @foreach($autores ?? [] as $autor)
                                    <option value="{{ $autor->id }}"
                                        {{ old('autor_id', $livro->autor_id ?? '') == $autor->id ? 'selected' : '' }}>
                                        {{ $autor->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('autor_id')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                            <label class="label">
                                <span class="label-text-alt text-base-content/70">Selecione um autor para este livro</span>
                            </label>
                        </div>

                        <!-- Bibliografia -->
                        <div class="form-control col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Bibliografia</span>
                            </label>
                            <textarea name="bibliografia"
                                      rows="4"
                                      class="textarea textarea-bordered w-full @error('bibliografia') textarea-error @enderror"
                                      placeholder="Descrição, sinopse, notas...">{{ old('bibliografia', $livro->bibliografia ?? '') }}</textarea>
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
                            <input type="file"
                                   name="imagem_capa"
                                   class="file-input file-input-bordered w-full @error('imagem_capa') file-input-error @enderror"
                                   accept="image/*" />
                            @error('imagem_capa')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            @if(isset($livro) && $livro->imagem_capa)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($livro->imagem_capa) }}"
                                         class="w-20 h-24 object-cover rounded"
                                         alt="Capa atual">
                                    <span class="text-sm text-base-content/50">Imagem atual</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.livros') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($livro) ? 'Atualizar Livro' : 'Criar Livro' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
