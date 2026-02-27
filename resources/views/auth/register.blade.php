<x-guest-layout>
    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Nome</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="input input-bordered w-full @error('name') input-error @enderror"
                   placeholder="Seu nome completo" />
            @error('name')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Email</span>
            </label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="input input-bordered w-full @error('email') input-error @enderror"
                   placeholder="seu@email.com" />
            @error('email')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Password</span>
            </label>
            <input type="password" name="password" required
                   class="input input-bordered w-full @error('password') input-error @enderror"
                   placeholder="••••••••" />
            @error('password')
            <label class="label">
                <span class="label-text-alt text-error">{{ $message }}</span>
            </label>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-control mb-6">
            <label class="label">
                <span class="label-text">Confirmar Password</span>
            </label>
            <input type="password" name="password_confirmation" required
                   class="input input-bordered w-full"
                   placeholder="••••••••" />
        </div>

        <!-- Terms and Conditions (checkbox à direita) -->
        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="form-control mb-6">
                <label class="cursor-pointer label">
                    <span class="label-text">
                        Concordo com os
                        <a href="{{ route('terms.show') }}" class="link link-primary" target="_blank">Termos</a>
                        e
                        <a href="{{ route('policy.show') }}" class="link link-primary" target="_blank">Política de Privacidade</a>
                    </span>
                    <input type="checkbox" name="terms" class="checkbox checkbox-primary" required />
                </label>
            </div>
        @endif

        <div class="mt-6">
            <button type="submit" class="btn btn-primary w-full">
                Registar
            </button>
        </div>

        <div class="divider my-6">OU</div>

        <p class="text-center text-sm text-base-content/60">
            Já tem conta?
            <a href="{{ route('login') }}" class="link link-primary font-medium">
                Entrar
            </a>
        </p>
    </form>
</x-guest-layout>
