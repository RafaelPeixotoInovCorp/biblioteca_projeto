<nav x-data="{ open: false }" class="bg-base-100 border-b border-base-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo da Biblioteca -->
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/livro.png') }}"
                         alt="Logo Biblioteca"
                         style="height: 36px; width: auto; max-width: 36px; object-fit: contain;">
                    <span class="text-xl font-bold text-base-content hidden sm:inline">Biblioteca</span>
                </a>
            </div>

            <!-- Theme Toggle e Dropdown do Utilizador -->
            <div class="flex items-center gap-4">

                <!-- Dropdown do Utilizador -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-base-300 text-sm leading-4 font-medium rounded-md text-base-content bg-base-100 hover:text-base-content/70 hover:bg-base-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-base-content/60 bg-base-100">
                            {{ __('Minha Conta') }}
                        </div>

                        <x-dropdown-link href="{{ route('profile.show') }}" class="text-base-content hover:bg-base-200 bg-base-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-dropdown-link href="{{ route('api-tokens.index') }}" class="text-base-content hover:bg-base-200 bg-base-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                {{ __('API Tokens') }}
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-base-300 my-1"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" class="text-base-content hover:bg-base-200 bg-base-100"
                                             @click.prevent="$root.submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Sair') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-base-content/60 hover:text-base-content hover:bg-base-200 focus:outline-none focus:bg-base-200 focus:text-base-content transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-base-100">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Dashboard link for mobile -->
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 text-base-content hover:bg-base-200 {{ request()->routeIs('dashboard') ? 'bg-base-200 font-medium' : '' }}">
                Dashboard
            </a>

            @if(auth()->user()->canViewBooks())
                <a href="{{ route('livros.index') }}"
                   class="block px-4 py-2 text-base-content hover:bg-base-200 {{ request()->routeIs('livros.index') ? 'bg-base-200 font-medium' : '' }}">
                    Livros
                </a>
            @endif

            @if(auth()->user()->canViewAuthors())
                <a href="{{ route('autores.index') }}"
                   class="block px-4 py-2 text-base-content hover:bg-base-200 {{ request()->routeIs('autores.index') ? 'bg-base-200 font-medium' : '' }}">
                    Autores
                </a>
            @endif

            @if(auth()->user()->canViewPublishers())
                <a href="{{ route('editoras.index') }}"
                   class="block px-4 py-2 text-base-content hover:bg-base-200 {{ request()->routeIs('editoras.index') ? 'bg-base-200 font-medium' : '' }}">
                    Editoras
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users') }}"
                   class="block px-4 py-2 text-base-content hover:bg-base-200 {{ request()->routeIs('admin.*') ? 'bg-base-200 font-medium' : '' }}">
                    Admin
                </a>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-base-300 bg-base-100">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-base-content">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-base-content/60">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">

                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-base-content">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')" class="text-base-content">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}"
                                           @click.prevent="$root.submit();" class="text-base-content">
                        {{ __('Sair') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
