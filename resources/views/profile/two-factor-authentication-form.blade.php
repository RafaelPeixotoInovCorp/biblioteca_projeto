<div x-data="{ recovery: false }">
    <div class="mb-4" x-show="! recovery">
        <p class="text-sm text-base-content/70">
            {{ __('Quando a autenticação de dois fatores está ativada, será solicitado um token seguro e aleatório durante a autenticação. Pode obter este token na aplicação Google Authenticator do seu telemóvel.') }}
        </p>
    </div>

    <div class="mb-4" x-show="recovery">
        <p class="text-sm text-base-content/70">
            {{ __('Por favor, confirme o acesso à sua conta introduzindo um dos seus códigos de recuperação de emergência.') }}
        </p>
    </div>

    @if (! $this->enabled)
        <!-- Ativar 2FA -->
        <div class="flex items-center justify-between">
            <div>
                <h4 class="font-medium text-base-content">Situação: <span class="badge badge-warning">Desativado</span></h4>
                <p class="text-sm text-base-content/70 mt-1">A autenticação de dois fatores está atualmente desativada.</p>
            </div>
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <button type="button" class="btn btn-primary" wire:loading.attr="disabled">
                    Ativar 2FA
                </button>
            </x-confirms-password>
        </div>
    @else
        <!-- Desativar 2FA ou mostrar códigos -->
        @if ($this->showingQrCode)
            <div class="mt-4 max-w-xl text-sm">
                <p class="font-semibold text-base-content">
                    @if ($this->showingConfirmation)
                        {{ __('Para finalizar a ativação da autenticação de dois fatores, scaneie o seguinte código QR usando a aplicação Google Authenticator ou introduza a chave de configuração e forneça o código gerado.') }}
                    @else
                        {{ __('A autenticação de dois fatores está agora ativada. Scaneie o seguinte código QR usando a aplicação Google Authenticator do seu telemóvel.') }}
                    @endif
                </p>
            </div>

            <div class="mt-4 p-2 inline-block bg-white">
                {!! $this->user->twoFactorQrCodeSvg() !!}
            </div>

            <div class="mt-4 max-w-xl text-sm">
                <p class="font-semibold">
                    {{ __('Chave de Configuração') }}: {{ decrypt($this->user->two_factor_secret) }}
                </p>
            </div>

            @if ($this->showingConfirmation)
                <div class="mt-4">
                    <x-label for="code" value="{{ __('Código') }}" />

                    <x-input id="code" type="text" name="code" class="block mt-1 w-1/2" inputmode="numeric" autofocus autocomplete="one-time-code"
                             wire:model="code"
                             wire:keydown.enter="confirmTwoFactorAuthentication" />

                    <x-input-error for="code" class="mt-2" />
                </div>
            @endif
        @endif

        @if ($this->showingRecoveryCodes)
            <div class="mt-4 max-w-xl text-sm">
                <p class="font-semibold">
                    {{ __('Guarde estes códigos de recuperação num gestor de palavras-passe seguro. Eles podem ser usados para recuperar o acesso à sua conta se o seu dispositivo de autenticação de dois fatores for perdido.') }}
                </p>
            </div>

            <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-base-200 rounded-lg">
                @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                    <div class="text-base-content">{{ $code }}</div>
                @endforeach
            </div>
        @endif

        <div class="mt-5 flex items-center gap-4">
            @if (! $this->showingRecoveryCodes && ! $this->showingConfirmation)
                <x-confirms-password wire:then="showRecoveryCodes">
                    <button type="button" class="btn btn-outline btn-sm">
                        Mostrar Códigos de Recuperação
                    </button>
                </x-confirms-password>
            @endif

            @if ($this->showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <button type="button" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
                        Confirmar
                    </button>
                </x-confirms-password>
            @endif

            @if (! $this->showingConfirmation)
                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <button type="button" class="btn btn-error btn-sm" wire:loading.attr="disabled">
                        Desativar 2FA
                    </button>
                </x-confirms-password>
            @endif
        </div>

        <div class="mt-4">
            <h4 class="font-medium text-base-content">Situação: <span class="badge badge-success">Ativado</span></h4>
        </div>
    @endif
</div>
