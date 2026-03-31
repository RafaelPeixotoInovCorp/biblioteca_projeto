<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encomenda extends Model
{
    protected $table = 'encomendas';

    protected $fillable = [
        'user_id',
        'numero_encomenda',
        'status',
        'subtotal',
        'total',
        'morada_entrega',
        'codigo_postal',
        'cidade',
        'telemovel',
        'observacoes',
        'stripe_payment_intent_id',
        'stripe_payment_status',
        'data_pagamento',
    ];

    protected $casts = [
        'data_pagamento' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemEncomenda::class);
    }

    public function isPago(): bool
    {
        return $this->status === 'pago';
    }

    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }
}
