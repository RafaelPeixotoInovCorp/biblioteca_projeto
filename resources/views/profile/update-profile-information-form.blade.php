<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <span class="text-base-content">{{ __('Informações do Perfil') }}</span>
    </x-slot>

    <x-slot name="description">
        <span class="text-base-content/70">{{ __('Atualize as informações do seu perfil e endereço de email.') }}</span>
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <input type="file" id="photo" class="hidden"
                       wire:model.live="photo"
                       x-ref="photo"
                       x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Foto') }}" class="text-base-content" />

                <div class="mt-2" x-show="! photoPreview">
                    <div class="avatar">
                        <div class="w-20 h-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}">
                        </div>
                    </div>
                </div>

                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <div class="avatar">
                        <div class="w-20 h-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <span class="block w-20 h-20 bg-cover bg-no-repeat bg-center"
                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex gap-2">
                    <button type="button" class="btn btn-outline" x-on:click.prevent="$refs.photo.click()">
                        {{ __('Selecionar Nova Foto') }}
                    </button>

                    @if ($this->user->profile_photo_path)
                        <button type="button" class="btn btn-ghost" wire:click="deleteProfilePhoto">
                            {{ __('Remover Foto') }}
                        </button>
                    @endif
                </div>

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Nome') }}" class="text-base-content" />
            <input id="name" type="text" class="input input-bordered w-full mt-1" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" class="text-base-content" />
            <input id="email" type="email" class="input input-bordered w-full mt-1" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-base-content/70">
                    {{ __('O seu email não foi verificado.') }}

                    <button type="button" class="link link-primary" wire:click.prevent="sendEmailVerification">
                        {{ __('Reenviar email de verificação.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-success">
                        {{ __('Um novo link de verificação foi enviado para o seu email.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex items-center gap-4">
            <span class="text-success" wire:loading.class="hidden" wire:target="photo">
                @if (session()->has('saved'))
                    {{ __('Guardado.') }}
                @endif
            </span>

            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="photo">
                {{ __('Guardar') }}
            </button>
        </div>
    </x-slot>
</x-form-section>
