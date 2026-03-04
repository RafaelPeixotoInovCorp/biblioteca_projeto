<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    protected $table = 'livros';

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco'
    ];

    protected $casts = [
        'preco' => 'decimal:2'
    ];

    // Cifrar dados sensíveis
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = encrypt($value);
    }

    public function getNomeAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function setBibliografiaAttribute($value)
    {
        $this->attributes['bibliografia'] = $value ? encrypt($value) : null;
    }

    public function getBibliografiaAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    // Relacionamentos
    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }

    // Relacionamento com requisições
    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    // Verificar se livro está disponível
    public function isDisponivel()
    {
        return $this->disponivel && !$this->requisicoes()
                ->whereIn('status', ['pendente', 'aprovado'])
                ->exists();
    }

    // Obter requisição ativa (se existir)
    public function getRequisicaoAtiva()
    {
        return $this->requisicoes()
            ->whereIn('status', ['pendente', 'aprovado'])
            ->first();
    }
}
