<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Detalhes da Requisição') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Voltar
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-lg mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Informações da Requisição -->
                <div class="lg:col-span-2">
                    <div class="bg-base-100 shadow-xl rounded-3xl p-8">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-2xl font-bold">Requisição {{ $requisicao->numero_requisicao }}</h3>
                            <div>
                                @if($requisicao->status == 'pendente')
                                    <span class="badge badge-warning badge-lg">Pendente</span>
                                @elseif($requisicao->status == 'aprovado')
                                    <span class="badge badge-success badge-lg">Aprovado</span>
                                @elseif($requisicao->status == 'entregue')
                                    <span class="badge badge-info badge-lg">Entregue</span>
                                @elseif($requisicao->status == 'cancelado')
                                    <span class="badge badge-ghost badge-lg">Cancelado</span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <span class="text-sm text-base-content/50">Data da Requisição</span>
                                <p class="text-lg font-medium">{{ $requisicao->data_requisicao->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-base-content/50">Data Prevista</span>
                                <p class="text-lg font-medium">{{ $requisicao->data_prevista_entrega->format('d/m/Y') }}</p>
                            </div>
                            @if($requisicao->data_efetiva_entrega)
                                <div>
                                    <span class="text-sm text-base-content/50">Data de Entrega</span>
                                    <p class="text-lg font-medium">{{ $requisicao->data_efetiva_entrega->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-base-content/50">Dias de Atraso</span>
                                    <p class="text-lg font-medium">{{ $requisicao->dias_atraso }}</p>
                                </div>
                            @endif
                        </div>

                        @if($requisicao->observacoes)
                            <div class="mt-6">
                                <span class="text-sm text-base-content/50">Observações</span>
                                <p class="mt-1">{{ $requisicao->observacoes }}</p>
                            </div>
                        @endif

                        @if(auth()->user()->isAdmin())
                            @if(in_array($requisicao->status, ['pendente', 'aprovado']))
                                <div class="mt-8 flex gap-4">
                                    <form action="{{ route('requisicoes.confirmar-entrega', $requisicao) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Confirmar entrega do livro?')">
                                            Confirmar Entrega
                                        </button>
                                    </form>

                                    @if($requisicao->status == 'pendente')
                                        <form action="{{ route('requisicoes.cancelar', $requisicao) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error" onclick="return confirm('Cancelar requisição?')">
                                                Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        @else
                            @if($requisicao->status == 'pendente')
                                <div class="mt-8">
                                    <form action="{{ route('requisicoes.cancelar', $requisicao) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-error" onclick="return confirm('Cancelar requisição?')">
                                            Cancelar Requisição
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Cards laterais -->
                <div class="space-y-6">
                    <!-- Card do Livro -->
                    <div class="bg-base-100 shadow-xl rounded-3xl p-6">
                        <h4 class="font-bold text-lg mb-4">Livro</h4>
                        <div class="flex gap-4">
                            <div class="avatar">
                                <div class="w-20 h-24 rounded">
                                    @if($requisicao->livro->imagem_capa)
                                        <img src="{{ Storage::url($requisicao->livro->imagem_capa) }}" alt="{{ $requisicao->livro->nome }}">
                                    @else
                                        <div class="bg-base-200 w-full h-full flex items-center justify-center">
                                            📚
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="font-bold">{{ $requisicao->livro->nome }}</p>
                                <p class="text-sm text-base-content/70">ISBN: {{ $requisicao->livro->isbn }}</p>
                                <p class="text-sm text-base-content/70">Editora: {{ $requisicao->livro->editora?->nome ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                        <!-- Card do Cidadão -->
                        <div class="bg-base-100 shadow-xl rounded-3xl p-6">
                            <h4 class="font-bold text-lg mb-4">Cidadão</h4>
                            <div class="flex gap-4">
                                @if($requisicao->cidadao->profile_photo_url)
                                    <div class="avatar">
                                        <div class="w-16 h-16 rounded-full">
                                            <img src="{{ $requisicao->cidadao->profile_photo_url }}" alt="{{ $requisicao->cidadao->name }}">
                                        </div>
                                    </div>
                                @else
                                    <div class="w-16 h-16 rounded-full bg-primary/20 flex items-center justify-center">
                                        <span class="text-2xl text-primary">{{ substr($requisicao->cidadao->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold">{{ $requisicao->cidadao->name }}</p>
                                    <p class="text-sm text-base-content/70">{{ $requisicao->cidadao->email }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
