<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\ItemEncomenda;
use App\Models\Carrinho;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class EncomendaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $encomendas = Encomenda::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $encomendas = Encomenda::with('itens.livro')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('encomendas.index', compact('encomendas'));
    }

    public function show(Encomenda $encomenda)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $encomenda->user_id !== $user->id) {
            abort(403);
        }

        return view('encomendas.show', compact('encomenda'));
    }

    public function criarPagamento(Request $request)
    {
        $user = Auth::user();

        $carrinho = Carrinho::with('itens.livro')
            ->where('user_id', $user->id)
            ->first();

        if (!$carrinho || $carrinho->itens->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'Carrinho vazio.');
        }

        $request->validate([
            'morada_entrega' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:20',
            'cidade' => 'required|string|max:100',
            'telemovel' => 'required|string|max:20',
            'observacoes' => 'nullable|string|max:500',
        ]);

        // Validar preços
        foreach ($carrinho->itens as $item) {
            if ($item->livro->preco === null || $item->livro->preco == 0) {
                return redirect()->route('carrinho.index')
                    ->with('error', "O livro {$item->livro->nome} não tem preço definido.");
            }
        }

        try {
            DB::beginTransaction();

            $subtotal = $carrinho->total;
            $total = $subtotal;

            $ultimoNumero = Encomenda::max('id') ?? 0;
            $numeroEncomenda = 'ENC-' . date('Ymd') . '-' . str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);

            $encomenda = Encomenda::create([
                'user_id' => $user->id,
                'numero_encomenda' => $numeroEncomenda,
                'status' => 'pendente',
                'subtotal' => $subtotal,
                'total' => $total,
                'morada_entrega' => $request->morada_entrega,
                'codigo_postal' => $request->codigo_postal,
                'cidade' => $request->cidade,
                'telemovel' => $request->telemovel,
                'observacoes' => $request->observacoes,
            ]);

            foreach ($carrinho->itens as $item) {
                ItemEncomenda::create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                ]);
            }

            // Limpar carrinho
            $carrinho->itens()->delete();

            DB::commit();
            return redirect()->route('encomendas.pagamento', $encomenda);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao criar encomenda: ' . $e->getMessage());
            return redirect()->route('carrinho.index')
                ->with('error', 'Erro ao processar a encomenda. Tente novamente.');
        }
    }

    public function pagamento(Request $request, Encomenda $encomenda)
    {
        $user = Auth::user();

        if ($encomenda->user_id !== $user->id) {
            abort(403);
        }

        if ($encomenda->status == 'pago') {
            return redirect()->route('encomendas.show', $encomenda)
                ->with('info', 'Esta encomenda já foi paga.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Verificar se já existe PaymentIntent
            if (!$encomenda->stripe_payment_intent_id) {
                $paymentIntent = PaymentIntent::create([
                    'amount' => round($encomenda->total * 100),
                    'currency' => 'eur',
                    'metadata' => [
                        'encomenda_id' => $encomenda->id,
                        'user_id' => $user->id,
                        'numero_encomenda' => $encomenda->numero_encomenda,
                    ],
                    'description' => "Encomenda {$encomenda->numero_encomenda}",
                ]);

                $encomenda->update([
                    'stripe_payment_intent_id' => $paymentIntent->id,
                ]);
            } else {
                $paymentIntent = PaymentIntent::retrieve($encomenda->stripe_payment_intent_id);
            }

            return view('encomendas.pagamento', [
                'encomenda' => $encomenda,
                'clientSecret' => $paymentIntent->client_secret,
                'stripeKey' => env('STRIPE_KEY'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao criar PaymentIntent: ' . $e->getMessage());
            return redirect()->route('encomendas.show', $encomenda)
                ->with('error', 'Erro ao iniciar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Confirma o pagamento após sucesso do Stripe
     */
    public function confirmarPagamento(Request $request, Encomenda $encomenda)
    {
        $user = Auth::user();

        if ($encomenda->user_id !== $user->id) {
            abort(403);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::retrieve($encomenda->stripe_payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                $encomenda->update([
                    'status' => 'pago',
                    'stripe_payment_status' => 'succeeded',
                    'data_pagamento' => now(),
                ]);

                // Limpar carrinho
                Carrinho::where('user_id', $user->id)->delete();

                return redirect()->route('encomendas.show', $encomenda)
                    ->with('success', 'Pagamento confirmado com sucesso! Encomenda registada.');
            }

            return redirect()->route('encomendas.show', $encomenda)
                ->with('error', 'O pagamento não foi concluído.');

        } catch (\Exception $e) {
            \Log::error('Erro ao confirmar pagamento: ' . $e->getMessage());
            return redirect()->route('encomendas.show', $encomenda)
                ->with('error', 'Erro ao confirmar pagamento: ' . $e->getMessage());
        }
    }
}
