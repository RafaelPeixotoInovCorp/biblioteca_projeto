<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Botão voltar -->
            <div class="mb-4">
                <a href="{{ route('livros.index') }}" class="btn btn-sm btn-ghost">
                    ← Voltar
                </a>
            </div>

            <div class="flex flex-col md:flex-row gap-8">
                <!-- Imagem -->
                <div class="md:w-1/3">
                    @if($livro->imagem_capa)
                        <img src="{{ Storage::url($livro->imagem_capa) }}"
                             alt="{{ $livro->nome }}"
                             class="w-full rounded-lg shadow-xl">
                    @else
                        <div class="bg-base-300 rounded-lg h-64 flex items-center justify-center">
                            <span class="text-6xl">📚</span>
                        </div>
                    @endif
                </div>

                <!-- Informações -->
                <div class="md:w-2/3">
                    <h1 class="text-3xl font-bold mb-4">{{ $livro->nome }}</h1>

                    @if($livro->autores->isNotEmpty())
                        <p class="mb-4">
                            <span class="font-semibold">Autor(es):</span>
                            {{ $livro->autores->pluck('nome')->implode(', ') }}
                        </p>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="font-semibold">ISBN:</span><br>
                            {{ $livro->isbn }}
                        </div>
                        <div>
                            <span class="font-semibold">Editora:</span><br>
                            {{ $livro->editora?->nome ?? 'N/A' }}
                        </div>
                    </div>

                    <!-- Preço -->
                    @if($livro->preco && $livro->preco > 0)
                        <div class="mb-8">
                            <span class="text-sm text-base-content/50 uppercase tracking-wider block mb-2">Preço</span>
                            <span class="text-5xl font-bold text-primary">{{ number_format($livro->preco, 2, ',', '.') }} €</span>
                        </div>
                    @else
                        <div class="mb-8">
                            <span class="text-sm text-base-content/50 uppercase tracking-wider block mb-2">Preço</span>
                            <span class="badge badge-info badge-lg py-3 px-4 text-base">Preço sob consulta</span>
                        </div>
                    @endif

                    @if($livro->bibliografia)
                        <div>
                            <span class="font-semibold">Sobre o livro:</span>
                            <p class="mt-2 text-base-content/70">{{ $livro->bibliografia }}</p>
                        </div>
                    @endif

                    <!-- Botão de requisição -->
                    @if(auth()->user() && !auth()->user()->isAdmin())
                        <div class="mt-8">
                            @if($livro->isDisponivel())
                                <a href="{{ route('livros.requisitar', $livro->id) }}" class="btn btn-primary btn-lg w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Requisitar este livro
                                </a>
                            @else
                                <button class="btn btn-disabled btn-lg w-full" disabled>
                                    Livro indisponível
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
