<div class="p-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-base-content">Livros</h2>

        @if($canManage)
            <a href="{{ route('admin.livros.novo') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Adicionar Livro
            </a>
        @endif
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="form-control">
            <label class="label">
                <span class="label-text">Pesquisar por título</span>
            </label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Título do livro..."
                   class="input input-bordered w-full" />
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por autor</span>
            </label>
            <select wire:model.live="filtro_autor" class="select select-bordered w-full">
                <option value="">Todos os autores</option>
                @foreach($autores as $autor)
                    <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Filtrar por editora</span>
            </label>
            <select wire:model.live="filtro_editora" class="select select-bordered w-full">
                <option value="">Todas as editoras</option>
                @foreach($editoras as $editora)
                    <option value="{{ $editora->id }}">{{ $editora->nome }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Barra de ordenação -->
    <div class="flex justify-between items-center mb-4">
        <div class="text-sm text-base-content/70">
            {{ $livros->total() }} livros encontrados
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-base-content/70">Ordenar por:</span>
            <div class="join">
                <button wire:click="sortBy('nome')" class="join-item btn btn-sm {{ $sortField === 'nome' ? 'btn-primary' : 'btn-ghost' }}">
                    Nome @if($sortField === 'nome')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                </button>
                <button wire:click="sortBy('autor')" class="join-item btn btn-sm {{ $sortField === 'autor' ? 'btn-primary' : 'btn-ghost' }}">
                    Autor @if($sortField === 'autor')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                </button>
                <button wire:click="sortBy('preco')" class="join-item btn btn-sm {{ $sortField === 'preco' ? 'btn-primary' : 'btn-ghost' }}">
                    Preço @if($sortField === 'preco')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif
                </button>
            </div>
        </div>
    </div>

    <!-- Grid de Livros -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($livros as $livro)
            @php
                $disponivel = $livro->isDisponivel();
                $jaNotificado = false;
                if(auth()->user() && !$disponivel) {
                    $jaNotificado = \App\Models\NotificacaoDisponibilidade::where('livro_id', $livro->id)
                        ->where('cidadao_id', auth()->user()->id)
                        ->where('notificado', false)
                        ->exists();
                }
            @endphp

            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 z-0">
                <figure class="px-4 pt-4">
                    <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}">
                        @if($livro->imagem_capa)
                            <img src="{{ Storage::url($livro->imagem_capa) }}" alt="{{ $livro->nome }}" class="rounded-xl h-48 w-full object-cover hover:opacity-90 transition-opacity">
                        @else
                            <div class="bg-base-200 rounded-xl h-48 w-full flex items-center justify-center hover:bg-base-300 transition-colors">
                                <span class="text-base-content/30 text-8xl">📚</span>
                            </div>
                        @endif
                    </a>
                </figure>

                <div class="card-body">
                    <h2 class="card-title text-2xl font-bold">
                        <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}" class="hover:text-primary transition-colors">
                            {{ $livro->nome }}
                        </a>
                    </h2>

                    @if($livro->autores->isNotEmpty())
                        <p class="text-base-content/70 -mt-2">de {{ $livro->autores->pluck('nome')->implode(', ') }}</p>
                    @endif

                    @if($livro->preco && $livro->preco > 0)
                        <div class="mt-2"><span class="text-2xl font-bold text-primary">{{ number_format($livro->preco, 2, ',', '.') }}€</span></div>
                    @else
                        <div class="mt-2"><span class="badge badge-info">Preço sob consulta</span></div>
                    @endif

                    @if($disponivel)
                        <div class="badge badge-success gap-2 mt-2 w-fit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Disponível
                        </div>
                    @else
                        <div class="badge badge-error gap-2 mt-2 w-fit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            Indisponível
                        </div>
                    @endif

                    <div class="card-actions justify-end mt-4 pt-4 border-t border-base-200">
                        @if($canManage)
                            <a href="{{ route('admin.livros.editar', $livro->id) }}" class="btn btn-sm btn-info">Editar</a>
                            <button wire:click="delete({{ $livro->id }})" wire:confirm="Tem a certeza que deseja eliminar este livro?" class="btn btn-sm btn-error">Eliminar</button>
                        @else
                            <div class="flex flex-col gap-2 w-full">
                                <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}" class="btn btn-outline btn-primary btn-sm w-full">Ver detalhes</a>

                                @if($disponivel)
                                    <a href="{{ route('livros.requisitar', $livro->id) }}" class="btn btn-primary btn-sm w-full">Requisitar</a>
                                @else
                                    @if($jaNotificado)
                                        <button onclick="cancelarNotificacao({{ $livro->id }})" class="btn btn-success btn-sm w-full" id="btn-cancelar-{{ $livro->id }}">
                                            Notificação ativa
                                        </button>
                                    @else
                                        <button onclick="notificarDisponibilidade({{ $livro->id }})" class="btn btn-warning btn-sm w-full">
                                            Avisar-me
                                        </button>
                                    @endif
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="alert alert-info shadow-lg">
                    <div><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Nenhum livro encontrado.</span></div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $livros->links() }}
    </div>
</div>

<script>
    // Funções globais
    window.notificarDisponibilidade = function(livroId) {
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
                    alert('✅ ' + data.message);
                    location.reload();
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar pedido.');
            });
    };

    window.cancelarNotificacao = function(livroId) {
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
                        alert('✅' + data.message);
                        location.reload();
                    } else {
                        alert('❌' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao cancelar.');
                });
        }
    };

    // Garantir que as funções estão disponíveis
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Scripts carregados');
        window.notificarDisponibilidade = notificarDisponibilidade;
        window.cancelarNotificacao = cancelarNotificacao;
    });
</script>
