<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Avaliar Livro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 overflow-hidden shadow-xl sm:rounded-lg p-8">
                <!-- Informações da Requisição -->
                <div class="mb-8 p-4 bg-base-200 rounded-lg">
                    <h3 class="font-bold text-lg mb-2">Requisição #{{ $requisicao->numero_requisicao }}</h3>
                    <p>Livro: <strong>{{ $requisicao->livro->nome }}</strong></p>
                    <p>Data de entrega: {{ $requisicao->data_efetiva_entrega->format('d/m/Y') }}</p>
                </div>

                <form action="{{ route('reviews.store', $requisicao) }}" method="POST">
                    @csrf

                    <!-- Nota (estrelas) -->
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-medium">Avaliação <span class="text-error">*</span></span>
                        </label>
                        <div class="rating rating-lg rating-half">
                            <input type="radio" name="nota" value="1" class="rating-hidden" />
                            <input type="radio" name="nota" value="0.5" class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="nota" value="1" class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="nota" value="1.5" class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="nota" value="2" class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="nota" value="2.5" class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="nota" value="3" class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="nota" value="3.5" class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="nota" value="4" class="mask mask-star-2 mask-half-2 bg-orange-400" />
                            <input type="radio" name="nota" value="4.5" class="mask mask-star-2 mask-half-1 bg-orange-400" />
                            <input type="radio" name="nota" value="5" class="mask mask-star-2 mask-half-2 bg-orange-400" checked />
                        </div>
                        @error('nota')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <!-- Comentário -->
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-medium">Comentário (opcional)</span>
                        </label>
                        <textarea name="comentario" rows="5"
                                  class="textarea textarea-bordered w-full @error('comentario') textarea-error @enderror"
                                  placeholder="Partilhe a sua opinião sobre o livro...">{{ old('comentario') }}</textarea>
                        @error('comentario')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <!-- Informação sobre moderação -->
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-bold">A sua review será moderada</p>
                            <p class="text-sm">Após submissão, a sua review ficará suspensa até ser aprovada por um administrador. Receberá um email com a confirmação.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('requisicoes.show', $requisicao) }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            Submeter Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
