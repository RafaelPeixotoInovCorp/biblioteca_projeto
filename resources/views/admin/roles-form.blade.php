<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ isset($role) ? 'Editar Role' : 'Novo Role' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ isset($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($role))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6 max-w-2xl mx-auto">
                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nome do Role <span class="text-error">*</span></span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $role->name ?? '') }}"
                                   class="input input-bordered w-full @error('name') input-error @enderror"
                                   placeholder="Ex: admin, gestor, editor"
                                   required />
                            @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Descrição</span>
                            </label>
                            <textarea name="description"
                                      rows="2"
                                      class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                                      placeholder="Descrição do role...">{{ old('description', $role->description ?? '') }}</textarea>
                            @error('description')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Permissões -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Permissões</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 max-h-64 overflow-y-auto p-4 border border-base-300 rounded-box bg-base-200">
                                @foreach($permissions ?? [] as $permission)
                                    <label class="cursor-pointer label justify-start gap-3">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               class="checkbox checkbox-primary"
                                            {{ in_array($permission->id, old('permissions', $rolePermissions ?? [])) ? 'checked' : '' }}>
                                        <span class="label-text">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/70">Selecione as permissões para este role</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.roles') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($role) ? 'Atualizar Role' : 'Criar Role' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
