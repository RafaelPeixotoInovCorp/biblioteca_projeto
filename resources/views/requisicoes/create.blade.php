<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Requisitar Livro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-8">
                <div class="flex gap-6 mb-8">
                    <!-- Imagem do livro -->
                    <div class="w-32 h-40">
                        @if($livro->imagem_capa)
                            <img src="{{ Storage::url($livro->imagem_capa) }}"
                                 alt="{{ $livro->nome }}"
                                 class="w-full h-full object-cover rounded-lg shadow-lg">
                        @else
                            <div class="w-full h-full bg-base-300 rounded-lg flex items-center justify-center">
                                <span class="text-4xl">📚</span>
                            </div>
                        @endif
                    </div>

                    <!-- Informações do livro -->
                    <div>
                        <h3 class="text-2xl font-bold mb-2">{{ $livro->nome }}</h3>
                        <p class="text-base-content/70 mb-1">ISBN: {{ $livro->isbn }}</p>
                        <p class="text-base-content/70 mb-1">Editora: {{ $livro->editora?->nome ?? 'N/A' }}</p>
                        @if($livro->autores->isNotEmpty())
                            <p class="text-base-content/70">Autor(es): {{ $livro->autores->pluck('nome')->implode(', ') }}</p>
                        @endif
                    </div>
                </div>

                <form action="{{ route('requisicoes.store', $livro) }}" method="POST">
                    @csrf

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-medium">Observações (opcional)</span>
                        </label>
                        <textarea name="observacoes" rows="3"
                                  class="textarea textarea-bordered w-full"
                                  placeholder="Alguma observação sobre esta requisição..."></textarea>
                    </div>

                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-bold">Informações importantes:</p>
                            <ul class="list-disc list-inside text-sm mt-2">
                                <li>A data prevista de entrega é 5 dias após a requisição</li>
                                <li>Receberá um email de confirmação com os detalhes</li>
                                <li>Receberá um lembrete no dia anterior à entrega</li>
                                <li>Pode cancelar a requisição enquanto estiver pendente</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('livros.show', $livro) }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            Confirmar Requisição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
