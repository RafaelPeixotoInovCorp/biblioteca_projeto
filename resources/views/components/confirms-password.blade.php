@props(['title' => __('Confirmar Password'), 'content' => __('Para sua segurança, por favor confirme a sua password para continuar.'), 'button' => __('Confirmar')])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
>
    {{ $slot }}
</span>

@once
    <x-dialog-modal wire:model.live="confirmingPassword">
        <x-slot name="title">
            <span class="text-base-content">{{ $title }}</span>
        </x-slot>

        <x-slot name="content">
            <span class="text-base-content/70">{{ $content }}</span>

            <div class="mt-4" x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
                <x-input type="password"
                         class="input input-bordered w-full mt-1"
                         placeholder="{{ __('Password') }}"
                         x-ref="confirmable_password"
                         wire:model="confirmablePassword"
                         wire:keydown.enter="confirmPassword" />

                <x-input-error for="confirmablePassword" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <button class="btn btn-ghost" wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
                {{ __('Cancelar') }}
            </button>

            <button class="btn btn-primary ms-3" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
                {{ $button }}
            </button>
        </x-slot>
    </x-dialog-modal>
@endonce
