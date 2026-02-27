<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Perfil') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <!-- Card de Informações do Perfil -->
                <div class="card bg-base-100 shadow-xl rounded-3xl mb-6">
                    <div class="card-body bg-base-100">
                        <h3 class="text-xl font-bold text-base-content mb-4">Informações do Perfil</h3>
                        <p class="text-base-content/70 mb-4">Atualize as informações do seu perfil e endereço de email.</p>
                        @livewire('profile.update-profile-information-form')
                    </div>
                </div>

                <div class="divider"></div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <!-- Card de Atualizar Password -->
                <div class="card bg-base-100 shadow-xl rounded-3xl mb-6">
                    <div class="card-body bg-base-100">
                        <h3 class="text-xl font-bold text-base-content mb-4">Atualizar Password</h3>
                        <p class="text-base-content/70 mb-4">Certifique-se que a sua conta usa uma palavra-passe longa e aleatória para se manter segura.</p>
                        @livewire('profile.update-password-form')
                    </div>
                </div>

                <div class="divider"></div>
            @endif

            <!-- Card de Autenticação 2FA -->
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="card bg-base-100 shadow-xl rounded-3xl mb-6">
                    <div class="card-body bg-base-100">
                        <h3 class="text-xl font-bold text-base-content mb-4">Autenticação de Dois Fatores (2FA)</h3>
                        <p class="text-base-content/70 mb-4">Adicione segurança extra à sua conta usando autenticação de dois fatores.</p>
                        @livewire('profile.two-factor-authentication-form')
                    </div>
                </div>

                <div class="divider"></div>
            @endif

            <!-- Card de Sessões do Browser -->
            <div class="card bg-base-100 shadow-xl rounded-3xl mb-6">
                <div class="card-body bg-base-100">
                    <h3 class="text-xl font-bold text-base-content mb-4">Sessões Ativas</h3>
                    <p class="text-base-content/70 mb-4">Gerir e terminar sessões ativas noutros browsers e dispositivos.</p>
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="divider"></div>

                <!-- Card de Eliminar Conta -->
                <div class="card bg-base-100 shadow-xl rounded-3xl">
                    <div class="card-body bg-base-100">
                        <h3 class="text-xl font-bold text-base-content mb-4">Eliminar Conta</h3>
                        <p class="text-base-content/70 mb-4">Eliminar permanentemente a sua conta.</p>
                        @livewire('profile.delete-user-form')
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
