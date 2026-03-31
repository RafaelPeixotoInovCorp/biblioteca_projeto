<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Pagamento') }} - {{ $encomenda->numero_encomenda }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-sm btn-ghost">
                    ← Voltar à encomenda
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Resumo da Encomenda -->
                <div class="bg-base-100 rounded-3xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Resumo da Encomenda</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($encomenda->itens as $item)
                            <div class="flex justify-between items-center border-b border-base-200 pb-2">
                                <div>
                                    <p class="font-medium">{{ $item->livro->nome }}</p>
                                    <p class="text-sm text-base-content/70">{{ $item->quantidade }} x {{ number_format($item->preco_unitario, 2, ',', '.') }} €</p>
                                </div>
                                <span class="font-bold">{{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }} €</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-base-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span class="text-primary">{{ number_format($encomenda->total, 2, ',', '.') }} €</span>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Pagamento Stripe -->
                <div class="bg-base-100 rounded-3xl shadow-xl p-6">
                    <h3 class="text-xl font-bold mb-4">Dados de Pagamento</h3>
                    <p class="text-sm text-base-content/70 mb-6">
                        Pagamento seguro processado pelo Stripe.
                    </p>

                    <form id="payment-form">
                        @csrf
                        <div id="payment-element" class="mb-4"></div>
                        <button id="submit-button" class="btn btn-primary w-full">
                            <span id="button-text">Pagar {{ number_format($encomenda->total, 2, ',', '.') }} €</span>
                            <span id="spinner" class="loading loading-spinner loading-sm hidden"></span>
                        </button>
                        <div id="error-message" class="text-error text-sm mt-4"></div>
                    </form>

                    <p class="text-xs text-base-content/50 text-center mt-6">
                        Ao prosseguir, concorda com os nossos Termos de Serviço.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ $stripeKey }}');
        const clientSecret = '{{ $clientSecret }}';
        let elements;

        async function initialize() {
            const appearance = {
                theme: 'stripe',
                variables: {
                    colorPrimary: '#4f46e5',
                    colorBackground: '#ffffff',
                    colorText: '#1f2937',
                    colorDanger: '#ef4444',
                    fontFamily: 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif',
                    spacingUnit: '4px',
                    borderRadius: '8px',
                },
            };

            elements = stripe.elements({ clientSecret, appearance });
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');
        }

        async function handleSubmit(e) {
            e.preventDefault();

            const submitButton = document.getElementById('submit-button');
            const buttonText = document.getElementById('button-text');
            const spinner = document.getElementById('spinner');
            const errorMessage = document.getElementById('error-message');

            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');
            errorMessage.textContent = '';

            const { error } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route("encomendas.pagamento.confirmar", $encomenda) }}',
                },
            });

            if (error) {
                errorMessage.textContent = error.message;
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        }

        initialize();
        document.getElementById('payment-form').addEventListener('submit', handleSubmit);
    </script>
</x-app-layout>
