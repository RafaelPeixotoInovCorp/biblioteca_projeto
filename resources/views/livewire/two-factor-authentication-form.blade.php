<div class="space-y-4">
    @if (! $enabled)
        <!-- Estado desativado -->
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-medium">Autenticação de Dois Fatores</h4>
                <p class="text-sm text-base-content/70">Adicione segurança extra à sua conta.</p>
                <span class="badge badge-warning mt-2">Desativado</span>
            </div>
            <button wire:click="enableTwoFactorAuthentication" class="btn btn-primary">
                Ativar 2FA
            </button>
        </div>
    @else
        <!-- Estado ativado -->
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-medium">Autenticação de Dois Fatores</h4>
                <span class="badge badge-success mt-2">Ativado</span>
            </div>
            <button wire:click="disableTwoFactorAuthentication" class="btn btn-error btn-sm">
                Desativar 2FA
            </button>
        </div>

        @if($showingQrCode)
            <div class="mt-4">
                <p class="text-sm mb-2">Scanneie o código QR com a app Google Authenticator:</p>
                <div class="p-4 bg-white inline-block rounded-lg">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>

                @if($showingConfirmation)
                    <div class="mt-4">
                        <label class="label">Código de verificação</label>
                        <input type="text" wire:model="code" class="input input-bordered w-48">
                        <button wire:click="confirmTwoFactorAuthentication" class="btn btn-primary btn-sm ml-2">
                            Confirmar
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($showingRecoveryCodes)
            <div class="mt-4">
                <p class="text-sm mb-2">Códigos de recuperação (guarde-os num local seguro):</p>
                <div class="grid grid-cols-2 gap-2 p-4 bg-base-200 rounded-lg">
                    @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                        <div class="font-mono text-sm">{{ $code }}</div>
                    @endforeach
                </div>
                <button wire:click="$set('showingRecoveryCodes', false)" class="btn btn-ghost btn-sm mt-2">
                    Ocultar códigos
                </button>
            </div>
        @endif
    @endif
</div>
