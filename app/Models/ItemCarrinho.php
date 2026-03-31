<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCarrinho extends Model
{
    protected $table = 'itens_carrinho';

    protected $fillable = [
        'carrinho_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
    ];

    public function carrinho(): BelongsTo
    {
        return $this->belongsTo(Carrinho::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
