<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Meu Carrinho') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert alert-success shadow-lg mb-4">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($carrinho->itens->isEmpty())
                <div class="bg-base-100 rounded-3xl shadow-xl p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-lg text-base-content/70 mb-4">O seu carrinho está vazio.</p>
                    <a href="{{ route('livros.index') }}" class="btn btn-primary">Continuar compras</a>
                </div>
            @else
                <div class="bg-base-100 rounded-3xl shadow-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                事业
                                    <th>Livro</th>
                                    <th>Preço</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                 </tr>
                            </thead>
                            <tbody>
                                @foreach($carrinho->itens as $item)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
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
                                    </td>
                                    <td>{{ number_format($item->preco_unitario, 2, ',', '.') }} €</td>
                                    <td>
                                        <form action="{{ route('carrinho.atualizar', $item) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1" max="10" class="input input-bordered input-sm w-20 text-center">
                                            <button type="submit" class="btn btn-sm btn-ghost ml-1">Atualizar</button>
                                        </form>
                                    </td>
                                    <td class="font-bold">{{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }} €</td>
                                    <td>
                                        <form action="{{ route('carrinho.remover', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-error btn-circle">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 border-t border-base-200">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-bold">Total:</span>
                            <span class="text-2xl font-bold text-primary">{{ number_format($carrinho->total, 2, ',', '.') }} €</span>
                        </div>
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('livros.index') }}" class="btn btn-ghost">Continuar compras</a>
                            <a href="{{ route('carrinho.checkout') }}" class="btn btn-primary">Finalizar compra</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
