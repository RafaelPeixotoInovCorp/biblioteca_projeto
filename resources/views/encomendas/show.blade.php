<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Encomenda') }}: {{ $encomenda->numero_encomenda }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('encomendas.index') }}" class="btn btn-sm btn-ghost">
                    ← Voltar às encomendas
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success shadow-lg mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-base-100 rounded-3xl shadow-xl p-8">
                <!-- Status -->
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-2xl font-bold">Detalhes da Encomenda</h3>
                    @if($encomenda->status == 'pendente')
                        <span class="badge badge-warning badge-lg">Pendente</span>
                    @elseif($encomenda->status == 'pago')
                        <span class="badge badge-success badge-lg">Pago</span>
                    @endif
                </div>

                <!-- Itens -->
                <div class="mb-8">
                    <h4 class="font-bold text-lg mb-4">Itens</h4>
                    <div class="border border-base-200 rounded-lg overflow-hidden">
                        @foreach($encomenda->itens as $item)
                            <div class="flex justify-between items-center p-4 border-b border-base-200 last:border-0">
                                <div class="flex items-center gap-4">
                                    <div class="avatar">
                                        <div class="w-12 h-16 rounded">
                                            @if($item->livro->imagem_capa)
                                                <img src="{{ Storage::url($item->livro->imagem_capa) }}" alt="{{ $item->livro->nome }}">
                                            @else
                                                <div class="bg-base-200 w-full h-full flex items-center justify-center">
                                                    📚
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $item->livro->nome }}</div>
                                        <div class="text-sm text-base-content/70">{{ $item->livro->autores->first()?->nome ?? 'Autor desconhecido' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div>{{ number_format($item->preco_unitario, 2, ',', '.') }} €</div>
                                    <div class="text-sm text-base-content/70">x{{ $item->quantidade }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Totais -->
                <div class="bg-base-200 rounded-lg p-4 mb-8">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>{{ number_format($encomenda->subtotal, 2, ',', '.') }} €</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-base-300">
                        <span>Total:</span>
                        <span class="text-primary">{{ number_format($encomenda->total, 2, ',', '.') }} €</span>
                    </div>
                </div>

                <!-- Dados de Entrega -->
                <div class="mb-8">
                    <h4 class="font-bold text-lg mb-4">Dados de Entrega</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-base-200 rounded-lg p-4">
                        <div>
                            <span class="text-sm text-base-content/50">Morada</span>
                            <p>{{ $encomenda->morada_entrega }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-base-content/50">Código Postal</span>
                            <p>{{ $encomenda->codigo_postal }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-base-content/50">Cidade</span>
                            <p>{{ $encomenda->cidade }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-base-content/50">Telemóvel</span>
                            <p>{{ $encomenda->telemovel }}</p>
                        </div>
                        @if($encomenda->observacoes)
                        <div class="md:col-span-2">
                            <span class="text-sm text-base-content/50">Observações</span>
                            <p>{{ $encomenda->observacoes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Botão de Pagamento (apenas se encomenda pendente) -->
                @if($encomenda->status == 'pendente')
                    <div class="mt-8">
                        <form action="{{ route('encomendas.pagamento', $encomenda) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Pagar agora
                            </button>
                        </form>
                        <p class="text-sm text-base-content/50 text-center mt-4">
                            O pagamento é processado de forma segura através do Stripe.
                        </p>
                    </div>
                @endif

                <!-- Datas -->
                <div class="text-sm text-base-content/50">
                    <p>Data da encomenda: {{ $encomenda->created_at->format('d/m/Y H:i') }}</p>
                    @if($encomenda->data_pagamento)
                        <p>Data de pagamento: {{ $encomenda->data_pagamento->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
