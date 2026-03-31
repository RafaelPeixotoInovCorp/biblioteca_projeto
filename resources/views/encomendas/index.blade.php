<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Minhas Encomendas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success shadow-lg mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($encomendas->isEmpty())
                <div class="bg-base-100 rounded-3xl shadow-xl p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-lg text-base-content/70 mb-4">Ainda não tem encomendas.</p>
                    <a href="{{ route('livros.index') }}" class="btn btn-primary">Começar a comprar</a>
                </div>
            @else
                <div class="bg-base-100 rounded-3xl shadow-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nº Encomenda</th>
                                    <th>Data</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                 </tr>
                            </thead>
                            <tbody>
                                @foreach($encomendas as $encomenda)
                                 <tr>
                                    <td class="font-mono">{{ $encomenda->numero_encomenda }}</td>
                                    <td>{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="font-bold">{{ number_format($encomenda->total, 2, ',', '.') }} €</td>
                                    <td>
                                        @if($encomenda->status == 'pendente')
                                            <span class="badge badge-warning">Pendente</span>
                                        @elseif($encomenda->status == 'pago')
                                            <span class="badge badge-success">Pago</span>
                                        @else
                                            <span class="badge badge-ghost">{{ $encomenda->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-sm btn-primary">Ver detalhes</a>
                                    </td>
                                 </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4">
                        {{ $encomendas->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
