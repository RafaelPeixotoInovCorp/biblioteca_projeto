<nav x-data="{ open: false }" class="bg-base-100 border-b border-base-300 shadow-sm relative z-10">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo e Navegação -->
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/livro.png') }}"
                         alt="Logo Biblioteca"
                         class="h-8 w-auto">
                    <span class="text-xl font-bold text-base-content hidden sm:inline">Biblioteca</span>
                </a>

                <!-- Links de navegação desktop - Sempre visíveis -->
                <div class="hidden sm:flex sm:items-center space-x-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                              {{ request()->routeIs('dashboard')
                                  ? 'bg-primary/10 text-primary'
                                  : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                        Dashboard
                    </a>

                    @if(auth()->user()->canViewBooks())
                        <a href="{{ route('livros.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                                  {{ request()->routeIs('livros.index')
                                      ? 'bg-primary/10 text-primary'
                                      : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                            Livros
                        </a>
                    @endif

                    <a href="{{ route('requisicoes.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                              {{ request()->routeIs('requisicoes.*')
                                  ? 'bg-primary/10 text-primary'
                                  : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                        Requisições
                    </a>

                    @if(auth()->user()->canViewAuthors())
                        <a href="{{ route('autores.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                                  {{ request()->routeIs('autores.index')
                                      ? 'bg-primary/10 text-primary'
                                      : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                            Autores
                        </a>
                    @endif

                    @if(auth()->user()->canViewPublishers())
                        <a href="{{ route('editoras.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                                  {{ request()->routeIs('editoras.index')
                                      ? 'bg-primary/10 text-primary'
                                      : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                            Editoras
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <!-- Dropdown Admin - Versão Corrigida -->
                        <div class="dropdown dropdown-hover dropdown-end">
                            <div tabindex="0" role="button" class="px-3 py-2 rounded-md text-sm font-medium
            {{ request()->routeIs('admin.*') ? 'bg-primary/10 text-primary' : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                                Admin
                                <svg class="h-4 w-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-60 p-2 shadow-lg">
                                <li class="menu-title"><span>Gestão</span></li>
                                <li>
                                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Utilizadores
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.roles') }}" class="{{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Roles
                                    </a>
                                </li>

                                <li class="menu-title mt-2"><span>Ferramentas</span></li>
                                <li>
                                    <a href="{{ route('admin.importar.index') }}" class="{{ request()->routeIs('admin.importar.*') ? 'active' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Importar Livros
                                    </a>
                                </li>

                                <li class="menu-title mt-2"><span>Moderação</span></li>
                                <li>
                                    <a href="{{ route('admin.reviews.index') }}" class="flex justify-between items-center {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Moderar Reviews
                                        </span>
                                        @php
                                            $reviewsPendentes = App\Models\Review::where('estado', 'suspenso')->count();
                                        @endphp
                                        @if($reviewsPendentes > 0)
                                            <span class="badge badge-error badge-sm">{{ $reviewsPendentes }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dropdown do Utilizador -->
            <div class="flex items-center relative" style="z-index: 9999;">
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                        @if(Auth::user()->profile_photo_url)
                            <div class="w-10 rounded-full">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            </div>
                        @else
                            <div class="bg-neutral text-neutral-content rounded-full w-10">
                                <span class="text-xl">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[9999] w-52 p-2 shadow-lg">
                        <li class="menu-title">
                            <span>{{ Auth::user()->name }}</span>
                        </li>
                        <li>
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Perfil
                            </a>
                        </li>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <li>
                                <a href="{{ route('api-tokens.index') }}" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    API Tokens
                                </a>
                            </li>
                        @endif

                        <li class="menu-title mt-2">
                            <span>Sessão</span>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 text-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Hamburger (mobile) -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-md text-base-content/60 hover:text-base-content hover:bg-base-200 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1 border-t border-base-200">
            <a href="{{ route('dashboard') }}"
               class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                      {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                Dashboard
            </a>

            @if(auth()->user()->canViewBooks())
                <a href="{{ route('livros.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('livros.index') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Livros
                </a>
            @endif

            <a href="{{ route('requisicoes.index') }}"
               class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                      {{ request()->routeIs('requisicoes.*') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                Requisições
            </a>

            @if(auth()->user()->canViewAuthors())
                <a href="{{ route('autores.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('autores.index') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Autores
                </a>
            @endif

            @if(auth()->user()->canViewPublishers())
                <a href="{{ route('editoras.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('editoras.index') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Editoras
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <div class="border-t border-base-200 my-2"></div>
                <div class="px-4 py-1 text-xs font-semibold text-base-content/50 uppercase tracking-wider">
                    Gestão Admin
                </div>
                <a href="{{ route('admin.users') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.users') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Utilizadores
                </a>
                <a href="{{ route('admin.roles') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.roles') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Roles
                </a>

                <div class="px-4 py-1 text-xs font-semibold text-base-content/50 uppercase tracking-wider mt-2">
                    Ferramentas
                </div>
                <a href="{{ route('admin.importar.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.importar.*') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Importar Livros
                </a>

                <div class="px-4 py-1 text-xs font-semibold text-base-content/50 uppercase tracking-wider mt-2">
                    Moderação
                </div>
                <a href="{{ route('admin.reviews.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.reviews.*') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    <span class="flex items-center justify-between">
                        <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            Moderar Reviews
                        </span>
                        @php
                            $reviewsPendentes = App\Models\Review::where('estado', 'suspenso')->count();
                        @endphp
                        @if($reviewsPendentes > 0)
                            <span class="badge badge-error badge-sm">{{ $reviewsPendentes }}</span>
                        @endif
                    </span>
                </a>
            @endif
        </div>
    </div>
</nav>
