<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ isset($editora) ? 'Editar Editora' : 'Nova Editora' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ isset($editora) ? route('admin.editoras.update', $editora->id) : route('admin.editoras.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($editora))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6 max-w-2xl mx-auto">
                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nome <span class="text-error">*</span></span>
                            </label>
                            <input type="text"
                                   name="nome"
                                   value="{{ old('nome', $editora->nome ?? '') }}"
                                   class="input input-bordered w-full @error('nome') input-error @enderror"
                                   placeholder="Nome da editora"
                                   required />
                            @error('nome')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Logótipo -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Logótipo</span>
                            </label>
                            <input type="file"
                                   name="logotipo"
                                   class="file-input file-input-bordered w-full @error('logotipo') file-input-error @enderror"
                                   accept="image/*" />
                            @error('logotipo')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            @if(isset($editora) && $editora->logotipo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($editora->logotipo) }}"
                                         class="w-20 h-20 object-cover rounded"
                                         alt="Logótipo atual">
                                    <span class="text-sm text-base-content/50">Logótipo atual</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.editoras') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($editora) ? 'Atualizar Editora' : 'Criar Editora' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
