<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Requisições') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Indicadores -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat bg-primary text-primary-content rounded-box shadow-lg">
                    <div class="stat-title text-primary-content/80">Requisições Ativas</div>
                    <div class="stat-value">{{ $indicadores['ativas'] }}</div>
                </div>

                <div class="stat bg-secondary text-secondary-content rounded-box shadow-lg">
                    <div class="stat-title text-secondary-content/80">Últimos 30 dias</div>
                    <div class="stat-value">{{ $indicadores['ultimos_30_dias'] }}</div>
                </div>

                <div class="stat bg-accent text-accent-content rounded-box shadow-lg">
                    <div class="stat-title text-accent-content/80">Entregues Hoje</div>
                    <div class="stat-value">{{ $indicadores['entregues_hoje'] }}</div>
                </div>
            </div>

            <!-- Tabela de Requisições -->
            <div class="bg-base-100 shadow-xl rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Nº Requisição</th>
                            <th>Livro</th>
                            @if(auth()->user()->isAdmin())
                                <th>Cidadão</th>
                            @endif
                            <th>Data Requisição</th>
                            <th>Data Prevista</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($requisicoes as $req)
                            <tr>
                                <td class="font-mono">{{ $req->numero_requisicao }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="w-10 h-12 rounded">
                                                @if($req->livro->imagem_capa)
                                                    <img src="{{ Storage::url($req->livro->imagem_capa) }}" alt="{{ $req->livro->nome }}">
                                                @else
                                                    <div class="bg-base-200 w-full h-full flex items-center justify-center">
                                                        📚
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $req->livro->nome }}</div>
                                        </div>
                                    </div>
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td>{{ $req->cidadao->name }}</td>
                                @endif
                                <td>{{ $req->data_requisicao->format('d/m/Y') }}</td>
                                <td>
                                    {{ $req->data_prevista_entrega->format('d/m/Y') }}
                                    @if($req->isAtrasado())
                                        <span class="badge badge-error ml-1">Atrasado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status == 'pendente')
                                        <span class="badge badge-warning">Pendente</span>
                                    @elseif($req->status == 'aprovado')
                                        <span class="badge badge-success">Aprovado</span>
                                    @elseif($req->status == 'entregue')
                                        <span class="badge badge-info">Entregue</span>
                                    @else
                                        <span class="badge badge-ghost">{{ $req->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('requisicoes.show', $req) }}" class="btn btn-sm btn-primary">
                                        Ver Detalhes
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="text-center py-8">
                                    Nenhuma requisição encontrada.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $requisicoes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
