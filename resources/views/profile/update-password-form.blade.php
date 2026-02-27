<x-form-section submit="updatePassword">
    <x-slot name="title">
        <span class="text-base-content">{{ __('Atualizar Password') }}</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-base-content/70">{{ __('Certifique-se que a sua conta usa uma palavra-passe longa e aleatória para se manter segura.') }}</span>
    </x-slot>

    <x-slot name="form">
        <!-- Current Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="current_password" value="{{ __('Password Atual') }}" class="text-base-content" />
            <input id="current_password" type="password" class="input input-bordered w-full mt-1" wire:model="state.current_password" autocomplete="current-password" />
            <x-input-error for="current_password" class="mt-2" />
        </div>

        <!-- New Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password" value="{{ __('Nova Password') }}" class="text-base-content" />
            <input id="password" type="password" class="input input-bordered w-full mt-1" wire:model="state.password" autocomplete="new-password" />
            <x-input-error for="password" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="password_confirmation" value="{{ __('Confirmar Password') }}" class="text-base-content" />
            <input id="password_confirmation" type="password" class="input input-bordered w-full mt-1" wire:model="state.password_confirmation" autocomplete="new-password" />
            <x-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-4">
            <span class="text-success" wire:loading.class="hidden" wire:target="password">
                @if (session()->has('saved'))
                    {{ __('Guardado.') }}
                @endif
            </span>

            <button type="submit" class="btn btn-primary">
                {{ __('Guardar') }}
            </button>
        </div>
    </x-slot>
</x-form-section>
