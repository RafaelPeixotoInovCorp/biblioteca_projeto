<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Editora extends Model
{
    protected $table = 'editoras';

    protected $fillable = [
        'nome',
        'logotipo'
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
    public function livros(): HasMany
    {
        return $this->hasMany(Livro::class);
    }
}
