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
                        <div class="mt-8 space-y-4">
                            @if($livro->isDisponivel())
                                <a href="{{ route('livros.requisitar', $livro->id) }}" class="btn btn-primary btn-lg w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Requisitar este livro
                                </a>
                            @else
                                @php
                                    $jaNotificado = \App\Models\NotificacaoDisponibilidade::where('livro_id', $livro->id)
                                        ->where('cidadao_id', auth()->user()->id)
                                        ->where('notificado', false)
                                        ->exists();
                                @endphp

                                @if($jaNotificado)
                                    <div class="alert alert-success shadow-lg">
                                        <div>

                                            <span>Está inscrito para receber notificação quando este livro ficar disponível.</span>
                                        </div>
                                        <button onclick="cancelarNotificacaoDetalhe({{ $livro->id }})" class="btn btn-sm btn-ghost">
                                            Cancelar
                                        </button>
                                    </div>
                                @else
                                    <div class="card bg-base-200">
                                        <div class="card-body">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 rounded-full bg-warning/20 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="font-bold text-lg">Livro indisponível</h3>
                                                    <p class="text-sm text-base-content/70">Receba um email quando este livro voltar a estar disponível.</p>
                                                </div>
                                            </div>
                                            <div class="card-actions justify-end mt-4">
                                                <button onclick="notificarDisponibilidadeDetalhe({{ $livro->id }})" class="btn btn-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                    Avisar-me quando disponível
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Secção de Reviews -->
            @if($livro->reviews()->where('estado', 'ativo')->exists())
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6">Avaliações dos Leitores</h2>

                    @php
                        $media = $livro->reviews()->where('estado', 'ativo')->avg('nota');
                        $total = $livro->reviews()->where('estado', 'ativo')->count();
                    @endphp

                    <div class="flex items-center gap-6 mb-8 p-4 bg-base-200 rounded-lg">
                        <div class="text-center">
                            <span class="text-4xl font-bold text-primary">{{ number_format($media, 1) }}</span>
                            <span class="text-base-content/70">/5</span>
                            <div class="flex mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($media))
                                        <span class="text-yellow-400 text-xl">★</span>
                                    @else
                                        <span class="text-base-300 text-xl">★</span>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div>
                            <p class="text-lg">Baseado em {{ $total }} {{ $total == 1 ? 'avaliação' : 'avaliações' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($livro->reviews()->with('cidadao')->where('estado', 'ativo')->latest()->get() as $review)
                            <div class="bg-base-100 border border-base-200 rounded-lg p-6">
                                <div class="flex items-center gap-4 mb-3">
                                    @if($review->cidadao->profile_photo_url)
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded-full">
                                                <img src="{{ $review->cidadao->profile_photo_url }}" alt="{{ $review->cidadao->name }}">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                            <span class="text-primary">{{ substr($review->cidadao->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold">{{ $review->cidadao->name }}</p>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->nota)
                                                    <span class="text-yellow-400">★</span>
                                                @else
                                                    <span class="text-base-300">★</span>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-sm text-base-content/50 ml-auto">{{ $review->created_at->diffForHumans() }}</span>
                                </div>

                                @if($review->comentario)
                                    <p class="text-base-content/80">{{ $review->comentario }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Livros Relacionados -->
            @if(isset($livrosRelacionados) && count($livrosRelacionados) > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-base-content mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Quem viu este livro também viu
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($livrosRelacionados as $relacionado)
                            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 group">
                                <figure class="px-4 pt-4">
                                    <a href="{{ route('livros.show', ['id' => $relacionado->id, 'slug' => \Str::slug($relacionado->nome)]) }}">
                                        @if($relacionado->imagem_capa)
                                            <img src="{{ Storage::url($relacionado->imagem_capa) }}" alt="{{ $relacionado->nome }}" class="rounded-xl h-48 w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="bg-base-200 rounded-xl h-48 w-full flex items-center justify-center group-hover:bg-base-300 transition-colors">
                                                <span class="text-6xl">📚</span>
                                            </div>
                                        @endif
                                    </a>
                                </figure>

                                <div class="card-body">
                                    <h3 class="card-title text-base">
                                        <a href="{{ route('livros.show', ['id' => $relacionado->id, 'slug' => \Str::slug($relacionado->nome)]) }}" class="hover:text-primary transition-colors line-clamp-2">
                                            {{ $relacionado->nome }}
                                        </a>
                                    </h3>

                                    @if($relacionado->autores->isNotEmpty())
                                        <p class="text-sm text-base-content/70">{{ $relacionado->autores->first()->nome }}</p>
                                    @endif

                                    @if($relacionado->preco && $relacionado->preco > 0)
                                        <p class="text-lg font-bold text-primary mt-2">{{ number_format($relacionado->preco, 2, ',', '.') }} €</p>
                                    @else
                                        <p class="text-sm badge badge-info badge-outline mt-2">Preço sob consulta</p>
                                    @endif

                                    <div class="card-actions justify-end mt-2">
                                        <a href="{{ route('livros.show', ['id' => $relacionado->id, 'slug' => \Str::slug($relacionado->nome)]) }}" class="btn btn-sm btn-primary">Ver detalhes</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function notificarDisponibilidadeDetalhe(livroId) {
            console.log('A notificar para livro:', livroId);

            fetch(`/livros/${livroId}/notificar-disponibilidade`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Resposta:', data);
                    if (data.success) {
                        alert('✅' + data.message);
                        location.reload();
                    } else {
                        alert('❌' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar pedido.');
                });
        }

        function cancelarNotificacaoDetalhe(livroId) {
            console.log('A cancelar notificação para livro:', livroId);

            if (confirm('Deseja cancelar a notificação para este livro?')) {
                fetch(`/livros/${livroId}/cancelar-notificacao`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Resposta:', data);
                        if (data.success) {
                            alert('✅ ' + data.message);
                            location.reload();
                        } else {
                            alert('❌ ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao cancelar.');
                    });
            }
        }
    </script>
</x-app-layout>
