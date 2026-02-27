<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    protected $table = 'autors';  // Nome da tabela na base de dados

    protected $fillable = [
        'nome',
        'foto'
    ];

    // Cifrar nome
    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = encrypt($value);
    }

    public function getNomeAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    // Relacionamentos
    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }
}
