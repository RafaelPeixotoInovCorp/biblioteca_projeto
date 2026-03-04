<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

    protected $fillable = [
        'numero_requisicao',
        'livro_id',
        'cidadao_id',
        'admin_id',
        'data_requisicao',
        'data_prevista_entrega',
        'data_efetiva_entrega',
        'dias_atraso',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_requisicao' => 'date',
        'data_prevista_entrega' => 'date',
        'data_efetiva_entrega' => 'date',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pendente', 'aprovado']);
    }

    public function isAtrasado(): bool
    {
        return $this->status === 'aprovado' &&
            $this->data_prevista_entrega < now()->format('Y-m-d');
    }
}
