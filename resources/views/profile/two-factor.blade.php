<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Autenticação de Dois Fatores (2FA)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if(session('success'))
                    <div class="alert alert-success shadow-lg mb-6">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error shadow-lg mb-6">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(!$enabled)
                    <!-- DESATIVADO -->
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <h3 class="card-title text-2xl mb-4">Ativar Autenticação de Dois Fatores</h3>

                            <div class="badge badge-warning badge-lg mb-6">Atualmente: Desativado</div>

                            <p class="mb-6 text-base-content/80">
                                A autenticação de dois fatores adiciona uma camada extra de segurança à sua conta.
                                Após ativar, será necessário um código de 6 dígitos gerado pelo Google Authenticator
                                sempre que fizer login.
                            </p>

                            <form method="POST" action="{{ route('profile.two-factor.enable') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    Ativar 2FA
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(session('show_qr'))
                        <div class="card bg-base-200 mt-6">
                            <div class="card-body">
                                <h4 class="font-bold text-xl mb-4">Passo 1: Scanneie o código QR</h4>

                                <div class="flex justify-center p-6 bg-white rounded-lg mb-6">
                                    {!! $qrCode !!}
                                </div>

                                <h4 class="font-bold text-xl mb-4">Passo 2: Introduza o código de verificação</h4>

                                <form method="POST" action="{{ route('profile.two-factor.confirm') }}">
                                    @csrf

                                    <div class="form-control max-w-xs">
                                        <label class="label">
                                            <span class="label-text">Código de 6 dígitos</span>
                                        </label>
                                        <input type="text"
                                               name="code"
                                               maxlength="6"
                                               class="input input-bordered text-center text-2xl @error('code') input-error @enderror"
                                               placeholder="123456"
                                               required>

                                        @error('code')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-4">
                                        Confirmar e Ativar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @else
                    <!-- ATIVADO -->
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="card-title text-2xl mb-4">2FA Ativo</h3>
                                    <span class="badge badge-success badge-lg mb-4">Ativado</span>
                                </div>

                                <form method="POST" action="{{ route('profile.two-factor.disable') }}"
                                      onsubmit="return confirm('Tem a certeza que deseja desativar o 2FA?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error">
                                        Desativar 2FA
                                    </button>
                                </form>
                            </div>

                            <!-- Códigos de recuperação -->
                            @if(count($recoveryCodes) > 0)
                                <div class="mt-8">
                                    <h4 class="font-bold text-xl mb-3">Códigos de recuperação</h4>
                                    <p class="text-sm text-warning mb-4">
                                        Guarde estes códigos num local seguro. Cada código só pode ser usado uma vez.
                                    </p>

                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($recoveryCodes as $code)
                                            <div class="bg-base-300 p-3 rounded text-center font-mono text-sm">
                                                {{ $code }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
