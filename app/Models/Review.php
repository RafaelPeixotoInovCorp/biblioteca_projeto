<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'requisicao_id',
        'livro_id',
        'cidadao_id',
        'nota',
        'comentario',
        'estado',
        'justificacao_recusa',
        'moderado_por',
        'moderado_em',
    ];

    protected $casts = [
        'nota' => 'integer',
        'moderado_em' => 'datetime',
    ];

    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderado_por');
    }

    public function isAtivo(): bool
    {
        return $this->estado === 'ativo';
    }

    public function isSuspenso(): bool
    {
        return $this->estado === 'suspenso';
    }

    public function isRecusado(): bool
    {
        return $this->estado === 'recusado';
    }
}
