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

                    @if($livro->preco)
                        <p class="mb-4">
                            <span class="font-semibold">Preço:</span><br>
                            <span class="text-2xl text-primary">{{ number_format($livro->preco, 2, ',', '.') }} €</span>
                        </p>
                    @endif

                    @if($livro->bibliografia)
                        <div>
                            <span class="font-semibold">Sobre o livro:</span>
                            <p class="mt-2 text-base-content/70">{{ $livro->bibliografia }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
