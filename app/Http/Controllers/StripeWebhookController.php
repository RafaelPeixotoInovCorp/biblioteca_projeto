<?php

namespace App\Http\Controllers;

use App\Models\Encomenda;
use App\Models\Carrinho;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Event;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $encomenda = Encomenda::where('stripe_payment_intent_id', $paymentIntent->id)->first();

                if ($encomenda && $encomenda->status !== 'pago') {
                    $encomenda->update([
                        'status' => 'pago',
                        'stripe_payment_status' => 'succeeded',
                        'data_pagamento' => now(),
                    ]);

                    // Limpar carrinho
                    Carrinho::where('user_id', $encomenda->user_id)->delete();

                    Log::info('Payment succeeded', ['encomenda_id' => $encomenda->id]);
                }
                break;

            case 'payment_intent.payment_failed':
                Log::warning('Payment failed', ['payment_intent' => $event->data->object->id]);
                break;
        }

        return response()->json(['status' => 'success']);
    }
}
