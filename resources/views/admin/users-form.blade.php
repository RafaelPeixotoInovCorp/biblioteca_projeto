<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ isset($user) ? 'Editar Utilizador' : 'Novo Utilizador' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-6 max-w-2xl mx-auto">
                        <!-- Nome -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nome <span class="text-error">*</span></span>
                            </label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name ?? '') }}"
                                   class="input input-bordered w-full @error('name') input-error @enderror"
                                   placeholder="Nome completo"
                                   required />
                            @error('name')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Email <span class="text-error">*</span></span>
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email', $user->email ?? '') }}"
                                   class="input input-bordered w-full @error('email') input-error @enderror"
                                   placeholder="email@exemplo.com"
                                   required />
                            @error('email')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">{{ isset($user) ? 'Nova Password (deixar em branco para manter)' : 'Password' }} <span class="text-error">*</span></span>
                            </label>
                            <input type="password"
                                   name="password"
                                   class="input input-bordered w-full @error('password') input-error @enderror"
                                {{ !isset($user) ? 'required' : '' }} />
                            @error('password')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <!-- Confirmar Password -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Confirmar Password</span>
                            </label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="input input-bordered w-full"
                                {{ !isset($user) ? 'required' : '' }} />
                        </div>

                        <!-- Roles -->
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Roles</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 max-h-64 overflow-y-auto p-4 border border-base-300 rounded-box bg-base-200">
                                @foreach($roles ?? [] as $role)
                                    <label class="cursor-pointer label justify-start gap-3">
                                        <input type="checkbox"
                                               name="roles[]"
                                               value="{{ $role->id }}"
                                               class="checkbox checkbox-primary"
                                            {{ in_array($role->id, old('roles', $userRoles ?? [])) ? 'checked' : '' }}>
                                        <span class="label-text">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <label class="label">
                                <span class="label-text-alt text-base-content/70">Selecione os roles para este utilizador</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('admin.users') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            {{ isset($user) ? 'Atualizar Utilizador' : 'Criar Utilizador' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
