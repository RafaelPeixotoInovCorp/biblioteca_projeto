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
                <div class="card bg-primary text-primary-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Requisições Ativas</h3>
                        <p class="text-4xl font-bold">{{ $indicadores['ativas'] }}</p>
                    </div>
                </div>

                <div class="card bg-secondary text-secondary-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Últimos 30 dias</h3>
                        <p class="text-4xl font-bold">{{ $indicadores['ultimos_30_dias'] }}</p>
                    </div>
                </div>

                <div class="card bg-accent text-accent-content shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title text-lg">Entregues Hoje</h3>
                        <p class="text-4xl font-bold">{{ $indicadores['entregues_hoje'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Tabela de Requisições -->
            <div class="bg-base-100 shadow-xl rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
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
                                            <div class="text-sm opacity-50">{{ $req->livro->isbn }}</div>
                                        </div>
                                    </div>
                                </td>
                                @if(auth()->user()->isAdmin())
                                    <td>
                                        <div class="flex items-center gap-2">
                                            @if($req->cidadao->profile_photo_url)
                                                <div class="avatar">
                                                    <div class="w-8 h-8 rounded-full">
                                                        <img src="{{ $req->cidadao->profile_photo_url }}" alt="{{ $req->cidadao->name }}">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                                                    <span class="text-sm text-primary">{{ substr($req->cidadao->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <span>{{ $req->cidadao->name }}</span>
                                        </div>
                                    </td>
                                @endif
                                <td>{{ $req->data_requisicao->format('d/m/Y') }}</td>
                                <td>
                                    {{ $req->data_prevista_entrega->format('d/m/Y') }}
                                    @if($req->isAtrasado())
                                        <span class="badge badge-error badge-sm ml-1">Atrasado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($req->status == 'pendente')
                                        <span class="badge badge-warning">Pendente</span>
                                    @elseif($req->status == 'aprovado')
                                        <span class="badge badge-success">Aprovado</span>
                                    @elseif($req->status == 'entregue')
                                        <span class="badge badge-info">Entregue</span>
                                    @elseif($req->status == 'cancelado')
                                        <span class="badge badge-ghost">Cancelado</span>
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
                                    <div class="alert alert-info shadow-lg">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Nenhuma requisição encontrada.</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-base-200">
                    {{ $requisicoes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
