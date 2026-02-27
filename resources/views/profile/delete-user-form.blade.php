<x-action-section>
    <x-slot name="title">
        <span class="text-base-content">{{ __('Eliminar Conta') }}</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-base-content/70">{{ __('Eliminar permanentemente a sua conta.') }}</span>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-base-content/70">
            {{ __('Uma vez eliminada a sua conta, todos os seus recursos e dados serão permanentemente apagados. Antes de eliminar a sua conta, por favor descarregue quaisquer dados ou informações que pretenda guardar.') }}
        </div>

        <div class="mt-5">
            <button wire:click="confirmUserDeletion" wire:loading.attr="disabled" class="btn btn-error">
                {{ __('Eliminar Conta') }}
            </button>
        </div>

        <x-dialog-modal wire:model.live="confirmingUserDeletion">
            <x-slot name="title">
                <span class="text-base-content">{{ __('Eliminar Conta') }}</span>
            </x-slot>

            <x-slot name="content">
                <span class="text-base-content/70">{{ __('Tem a certeza que deseja eliminar a sua conta? Uma vez eliminada a sua conta, todos os seus recursos e dados serão permanentemente apagados. Por favor, introduza a sua password para confirmar que pretende eliminar permanentemente a sua conta.') }}</span>

                <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password" class="input input-bordered w-3/4 mt-1"
                           autocomplete="current-password"
                           placeholder="{{ __('Password') }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button class="btn btn-ghost" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </button>

                <button class="btn btn-error ms-3" wire:click="deleteUser" wire:loading.attr="disabled">
                    {{ __('Eliminar Conta') }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
