<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Finalizar Compra') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-base-100 rounded-3xl shadow-xl p-8">
                <h3 class="text-2xl font-bold mb-6">Resumo da Encomenda</h3>

                <!-- Resumo dos itens -->
                <div class="mb-8">
                    @foreach($carrinho->itens as $item)
                        <div class="flex justify-between py-3 border-b border-base-200">
                            <div>
                                <span class="font-bold">{{ $item->livro->nome }}</span>
                                <span class="text-sm text-base-content/70 ml-2">x{{ $item->quantidade }}</span>
                            </div>
                            <span>{{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }} €</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between pt-4 font-bold text-lg">
                        <span>Total</span>
                        <span class="text-primary">{{ number_format($carrinho->total, 2, ',', '.') }} €</span>
                    </div>
                </div>

                <h3 class="text-2xl font-bold mb-6">Dados de Entrega</h3>

                <form action="{{ route('encomenda.pagamento.criar') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Morada de Entrega <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="morada_entrega" class="input input-bordered" required>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Código Postal <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="codigo_postal" class="input input-bordered" placeholder="1234-567" required>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Cidade <span class="text-error">*</span></span>
                            </label>
                            <input type="text" name="cidade" class="input input-bordered" required>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Telemóvel <span class="text-error">*</span></span>
                            </label>
                            <input type="tel" name="telemovel" class="input input-bordered" required>
                        </div>

                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium">Observações</span>
                            </label>
                            <textarea name="observacoes" rows="3" class="textarea textarea-bordered" placeholder="Alguma observação sobre a entrega?"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <a href="{{ route('carrinho.index') }}" class="btn btn-ghost">Voltar ao carrinho</a>
                        <button type="submit" class="btn btn-primary btn-lg">Prosseguir para pagamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
