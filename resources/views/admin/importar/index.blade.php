<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Importar Livros da Google Books') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens -->
            @if(session('success'))
                <div class="alert alert-success shadow-lg mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error shadow-lg mb-4">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning shadow-lg mb-4">
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <!-- Formulário de Pesquisa -->
            <div class="bg-base-100 shadow-xl rounded-3xl p-8 mb-8">
                <h3 class="text-2xl font-bold mb-6">Pesquisar Livros</h3>

                <form action="{{ route('admin.importar.pesquisar') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Termo de pesquisa</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text"
                                   name="query"
                                   class="input input-bordered flex-1"
                                   placeholder="Título, autor, ISBN... (ex: José Saramago)"
                                   required>
                            <select name="max_results" class="select select-bordered w-32">
                                <option value="10">10 resultados</option>
                                <option value="20" selected>20 resultados</option>
                                <option value="30">30 resultados</option>
                                <option value="40">40 resultados</option>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Pesquisar
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-4 text-sm text-base-content/70">
                    <p>🔍 Pesquise por títulos, autores, ISBN ou palavras-chave. Os resultados vêm da Google Books API.</p>
                </div>
            </div>

            <!-- Informação sobre API Key -->
            <div class="bg-base-200 rounded-3xl p-6">
                <h4 class="font-bold text-lg mb-2">ℹ️ Sobre a Google Books API</h4>
                <p class="text-sm text-base-content/70 mb-2">
                    Esta funcionalidade utiliza a API pública do Google Books para pesquisar livros.
                </p>
                @if(!env('GOOGLE_BOOKS_API_KEY'))
                    <div class="alert alert-warning shadow-lg">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>
                                <strong>API Key não configurada!</strong> Para melhores resultados, configure uma chave de API no ficheiro .env:<br>
                                <code class="bg-base-300 px-2 py-1 rounded mt-1 inline-block">GOOGLE_BOOKS_API_KEY=sua_chave_aqui</code>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
