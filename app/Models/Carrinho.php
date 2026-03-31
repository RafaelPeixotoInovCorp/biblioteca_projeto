<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrinho extends Model
{
    protected $table = 'carrinhos';

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemCarrinho::class);
    }

    public function getTotalAttribute()
    {
        return $this->itens->sum(function ($item) {
            return $item->quantidade * $item->preco_unitario;
        });
    }
}
