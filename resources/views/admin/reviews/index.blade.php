<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Moderação de Reviews') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card bg-warning text-warning-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Suspensas</h3>
                        <p class="text-4xl font-bold">{{ $estatisticas['suspensas'] }}</p>
                    </div>
                </div>

                <div class="card bg-success text-success-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Ativas</h3>
                        <p class="text-4xl font-bold">{{ $estatisticas['ativas'] }}</p>
                    </div>
                </div>

                <div class="card bg-error text-error-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Recusadas</h3>
                        <p class="text-4xl font-bold">{{ $estatisticas['recusadas'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Lista de Reviews -->
            <div class="bg-base-100 shadow-xl rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Cidadão</th>
                            <th>Nota</th>
                            <th>Comentário</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="w-10 h-12 rounded">
                                                @if($review->livro->imagem_capa)
                                                    <img src="{{ Storage::url($review->livro->imagem_capa) }}" alt="{{ $review->livro->nome }}">
                                                @else
                                                    <div class="bg-base-200 w-full h-full flex items-center justify-center">
                                                        📚
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="font-bold">{{ Str::limit($review->livro->nome, 30) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if($review->cidadao->profile_photo_url)
                                            <div class="avatar">
                                                <div class="w-8 h-8 rounded-full">
                                                    <img src="{{ $review->cidadao->profile_photo_url }}" alt="{{ $review->cidadao->name }}">
                                                </div>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                                                <span class="text-sm text-primary">{{ substr($review->cidadao->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <span>{{ $review->cidadao->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->nota)
                                                <span class="text-yellow-400 text-lg">★</span>
                                            @else
                                                <span class="text-base-300 text-lg">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>{{ Str::limit($review->comentario ?? 'Sem comentário', 50) }}</td>
                                <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-primary">
                                        Moderar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="alert alert-info shadow-lg">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Nenhuma review pendente de moderação.</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-base-200">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
