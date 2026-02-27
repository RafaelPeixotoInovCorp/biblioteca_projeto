<x-guest-layout>
    <x-validation-errors class="mb-4" />

    @session('status')
    <div class="mb-4 font-medium text-sm text-green-600">
        {{ $value }}
    </div>
    @endsession

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="form-control mb-4">
            <label class="label">
                <span class="label-text">Email</span>
            </label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
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

        <!-- Remember Me Checkbox (à direita) -->
        <div class="form-control mb-6">
            <label class="cursor-pointer label">
                <span class="label-text">Lembrar-me</span>
                <input type="checkbox" name="remember" class="checkbox checkbox-primary" checked />
            </label>
        </div>

        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                    Esqueceu-se da password?
                </a>
            @endif

            <button type="submit" class="btn btn-primary">
                Entrar
            </button>
        </div>

        <div class="divider my-6">OU</div>

        <p class="text-center text-sm text-base-content/60">
            Ainda não tem conta?
            <a href="{{ route('register') }}" class="link link-primary font-medium">
                Registar
            </a>
        </p>
    </form>
</x-guest-layout>
