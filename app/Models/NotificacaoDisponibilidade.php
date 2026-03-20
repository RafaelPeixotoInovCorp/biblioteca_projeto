<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificacaoDisponibilidade extends Model
{
    protected $table = 'notificacoes_disponibilidade';

    protected $fillable = [
        'livro_id',
        'cidadao_id',
        'notificado',
        'notificado_em',
    ];

    protected $casts = [
        'notificado' => 'boolean',
        'notificado_em' => 'datetime',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }
}
