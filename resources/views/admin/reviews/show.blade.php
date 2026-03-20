<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Moderar Review') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Botão voltar -->
            <div class="mb-6">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar à lista
                </a>
            </div>

            <!-- Review Details -->
            <div class="bg-base-100 shadow-xl rounded-3xl overflow-hidden">
                <div class="p-8">
                    <!-- Cabeçalho com status -->
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-2xl font-bold">Detalhes da Review</h3>
                        <span class="badge badge-warning badge-lg">Suspensa</span>
                    </div>

                    <!-- Informações do Livro -->
                    <div class="flex gap-6 mb-8 p-4 bg-base-200 rounded-lg">
                        <div class="w-20 h-24">
                            @if($review->livro->imagem_capa)
                                <img src="{{ Storage::url($review->livro->imagem_capa) }}"
                                     alt="{{ $review->livro->nome }}"
                                     class="w-full h-full object-cover rounded-lg">
                            @else
                                <div class="w-full h-full bg-base-300 rounded-lg flex items-center justify-center">
                                    <span class="text-3xl">📚</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-xl">{{ $review->livro->nome }}</h4>
                            <p class="text-base-content/70">ISBN: {{ $review->livro->isbn }}</p>
                            <p class="text-base-content/70">Editora: {{ $review->livro->editora?->nome ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Informações do Cidadão -->
                    <div class="flex items-center gap-4 mb-8 p-4 bg-base-200 rounded-lg">
                        @if($review->cidadao->profile_photo_url)
                            <div class="avatar">
                                <div class="w-16 h-16 rounded-full">
                                    <img src="{{ $review->cidadao->profile_photo_url }}" alt="{{ $review->cidadao->name }}">
                                </div>
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="text-2xl text-primary">{{ substr($review->cidadao->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-bold text-lg">{{ $review->cidadao->name }}</h4>
                            <p class="text-base-content/70">{{ $review->cidadao->email }}</p>
                        </div>
                    </div>

                    <!-- Review Content -->
                    <div class="mb-8">
                        <div class="flex items-center gap-4 mb-4">
                            <span class="text-lg font-semibold">Avaliação:</span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->nota)
                                        <span class="text-yellow-400 text-2xl">★</span>
                                    @else
                                        <span class="text-base-300 text-2xl">★</span>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        @if($review->comentario)
                            <div class="bg-base-200 p-4 rounded-lg">
                                <p class="text-base-content/80 whitespace-pre-line">{{ $review->comentario }}</p>
                            </div>
                        @else
                            <p class="text-base-content/50 italic">Nenhum comentário adicionado.</p>
                        @endif
                    </div>

                    <!-- Data de submissão -->
                    <p class="text-sm text-base-content/50 mb-8">
                        Submetida em {{ $review->created_at->format('d/m/Y \à\s H:i') }}
                    </p>

                    <!-- Ações de moderação -->
                    <div class="border-t border-base-200 pt-6">
                        <div class="flex gap-4">
                            <!-- Botão Aprovar -->
                            <form action="{{ route('admin.reviews.aprovar', $review) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-full" onclick="return confirm('Confirmar aprovação da review?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aprovar Review
                                </button>
                            </form>

                            <!-- Botão Abrir Modal de Recusa -->
                            <button class="btn btn-error flex-1" onclick="document.getElementById('recusarModal').showModal()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Recusar Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recusa -->
    <dialog id="recusarModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Recusar Review</h3>
            <form action="{{ route('admin.reviews.recusar', $review) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Justificação da recusa <span class="text-error">*</span></span>
                    </label>
                    <textarea name="justificacao"
                              class="textarea textarea-bordered w-full @error('justificacao') textarea-error @enderror"
                              rows="4"
                              placeholder="Explique ao cidadão por que motivo a review foi recusada..."
                              required></textarea>
                    <label class="label">
                        <span class="label-text-alt text-base-content/70">Esta justificação será enviada por email ao cidadão.</span>
                    </label>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('recusarModal').close()">Cancelar</button>
                    <button type="submit" class="btn btn-error">Confirmar Recusa</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>fechar</button>
        </form>
    </dialog>
</x-app-layout>
