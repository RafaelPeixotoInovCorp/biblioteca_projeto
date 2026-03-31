<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemEncomenda extends Model
{
    protected $table = 'itens_encomenda';

    protected $fillable = [
        'encomenda_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
    ];

    public function encomenda(): BelongsTo
    {
        return $this->belongsTo(Encomenda::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }
}
