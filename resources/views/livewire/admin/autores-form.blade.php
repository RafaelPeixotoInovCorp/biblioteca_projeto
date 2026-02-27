<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ isset($autorId) ? 'Editar Autor' : 'Novo Autor' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ isset($autorId) ? route('admin.autores.atualizar', $autorId) : route('admin.autores.salvar') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @if(isset($autorId))
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
                                   value="{{ old('nome', $autor->nome ?? '') }}"
                                   class="input input-bordered w-full @error('nome') input-error @enderror"
                                   placeholder="Nome do autor"
                                   required />
                            @error('nome')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Foto -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Foto</span>
                            </label>
                            <input type="file"
                                   name="foto"
                                   class="file-input file-input-bordered w-full @error('foto') file-input-error @enderror"
                                   accept="image/*" />
                            @error('foto')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror

                            @if(isset($autor) && $autor->foto)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($autor->foto) }}"
                                         class="w-20 h-20 object-cover rounded-full"
                                         alt="Foto atual">
                                    <span class="text-sm text-base-content/50">Foto atual</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.autores') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($autorId) ? 'Atualizar Autor' : 'Criar Autor' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
