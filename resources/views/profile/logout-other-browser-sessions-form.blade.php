<x-action-section>
    <x-slot name="title">
        <span class="text-base-content">{{ __('Sessões do Navegador') }}</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-base-content/70">{{ __('Gerir e terminar sessões ativas noutros navegadores e dispositivos.') }}</span>
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-base-content/70">
            {{ __('Se necessário, pode terminar todas as suas outras sessões em todos os seus dispositivos. Algumas das suas sessões recentes estão listadas abaixo; no entanto, esta lista pode não ser exaustiva. Se achar que a sua conta foi comprometida, deve também atualizar a sua password.') }}
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-5 space-y-6">
                @foreach ($this->sessions as $session)
                    <div class="flex items-center">
                        <div>
                            @if ($session->agent->isDesktop())
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-base-content/50">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-base-content/50">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            @endif
                        </div>

                        <div class="ms-3">
                            <div class="text-sm text-base-content">
                                {{ $session->agent->platform() ? $session->agent->platform() : __('Desconhecido') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Desconhecido') }}
                            </div>

                            <div>
                                <div class="text-xs text-base-content/50">
                                    {{ $session->ip_address }},

                                    @if ($session->is_current_device)
                                        <span class="text-success font-semibold">{{ __('Este dispositivo') }}</span>
                                    @else
                                        {{ __('Última atividade') }} {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex items-center mt-5">
            <button wire:click="confirmLogout" wire:loading.attr="disabled" class="btn btn-primary">
                {{ __('Terminar Outras Sessões') }}
            </button>

            <span class="ms-3 text-success" wire:loading.class="hidden" wire:target="confirmLogout">
                @if (session()->has('loggedOut'))
                    {{ __('Terminado.') }}
                @endif
            </span>
        </div>

        <x-dialog-modal wire:model.live="confirmingLogout">
            <x-slot name="title">
                <span class="text-base-content">{{ __('Terminar Outras Sessões') }}</span>
            </x-slot>

            <x-slot name="content">
                <span class="text-base-content/70">{{ __('Por favor, introduza a sua password para confirmar que pretende terminar as suas outras sessões em todos os seus dispositivos.') }}</span>

                <div class="mt-4" x-data="{}" x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
                    <input type="password" class="input input-bordered w-3/4 mt-1"
                           autocomplete="current-password"
                           placeholder="{{ __('Password') }}"
                           x-ref="password"
                           wire:model="password"
                           wire:keydown.enter="logoutOtherBrowserSessions" />

                    <x-input-error for="password" class="mt-2" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <button class="btn btn-ghost" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                    {{ __('Cancelar') }}
                </button>

                <button class="btn btn-primary ms-3"
                        wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                    {{ __('Terminar Outras Sessões') }}
                </button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>
</x-action-section>
