<nav x-data="{ open: false }" class="bg-base-100 border-b border-base-300 shadow-sm">
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

                <!-- Links de navegação desktop -->
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
                        <!-- Link para Admin em vez de título -->
                        <a href="{{ route('admin.users') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                                  {{ request()->routeIs('admin.users') || request()->routeIs('admin.roles')
                                      ? 'bg-primary/10 text-primary'
                                      : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                            Admin
                        </a>

                        <!-- Link para Importar Livros (separado) -->
                        <a href="{{ route('admin.importar.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out
                                  {{ request()->routeIs('admin.importar.*')
                                      ? 'bg-primary/10 text-primary'
                                      : 'text-base-content/70 hover:text-base-content hover:bg-base-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Importar
                        </a>
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

                        @if(auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.users') }}" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Gestão Admin
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.importar.index') }}" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Importar Livros
                                </a>
                            </li>
                        @endif

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
                    Administração
                </div>
                <a href="{{ route('admin.users') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.users') || request()->routeIs('admin.roles') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    Gestão Admin
                </a>
                <a href="{{ route('admin.importar.index') }}"
                   class="block px-4 py-2 text-base hover:bg-base-200 transition-colors duration-150 ease-in-out
                          {{ request()->routeIs('admin.importar.*') ? 'bg-primary/10 text-primary font-medium' : 'text-base-content/70' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Importar Livros
                </a>
            @endif
        </div>
    </div>
</nav>
