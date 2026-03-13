<?php

namespace App\Traits;

use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Support\Facades\Log;

trait NormalizaNomes
{
    /**
     * Normaliza um nome para pesquisa e comparação
     */
    public function normalizarNome(string $nome): string
    {
        if (empty($nome)) {
            return '';
        }

        $nome = trim($nome);
        $nome = preg_replace('/\s+/', ' ', $nome);
        $nomeLower = mb_strtolower($nome, 'UTF-8');
        $nomeLower = str_replace(['.', ',', ';', ':', '!', '?', '"', "'", '´', '`', '(', ')'], '', $nomeLower);

        return $nomeLower;
    }

    /**
     * Encontra ou cria um autor com normalização
     */
    public function encontrarOuCriarAutor(?string $nomeAutor)
    {
        if (empty($nomeAutor)) {
            return null;
        }

        $nomeAutor = trim($nomeAutor);
        Log::info('[NormalizaNomes] A processar autor', ['nome_original' => $nomeAutor]);

        $nomeNormalizado = $this->normalizarNome($nomeAutor);

        // Procurar por nome exato primeiro (mais rápido)
        $autor = Autor::where('nome', $nomeAutor)->first();
        if ($autor) {
            Log::info('[NormalizaNomes] Autor encontrado por nome exato', ['id' => $autor->id, 'nome' => $autor->nome]);
            return $autor;
        }

        // Se não encontrar, procurar por normalização
        $autores = Autor::all();
        foreach ($autores as $autor) {
            if ($this->normalizarNome($autor->nome) === $nomeNormalizado) {
                Log::info('[NormalizaNomes] Autor encontrado por normalização', ['id' => $autor->id, 'nome' => $autor->nome]);
                return $autor;
            }
        }

        // Não encontrou, criar novo
        Log::info('[NormalizaNomes] Autor não encontrado, a criar novo', ['nome' => $nomeAutor]);
        return Autor::create(['nome' => $nomeAutor]);
    }

    /**
     * Encontra ou cria uma editora com normalização
     */
    public function encontrarOuCriarEditora(?string $nomeEditora)
    {
        if (empty($nomeEditora) || $nomeEditora === 'Editora Desconhecida') {
            Log::info('[NormalizaNomes] Editora desconhecida, a usar padrão');
            return Editora::firstOrCreate(['nome' => 'Editora Desconhecida']);
        }

        $nomeEditora = trim($nomeEditora);
        Log::info('[NormalizaNomes] A processar editora', ['nome_original' => $nomeEditora]);

        $nomeNormalizado = $this->normalizarNome($nomeEditora);

        // Procurar por nome exato primeiro
        $editora = Editora::where('nome', $nomeEditora)->first();
        if ($editora) {
            Log::info('[NormalizaNomes] Editora encontrada por nome exato', ['id' => $editora->id, 'nome' => $editora->nome]);
            return $editora;
        }

        // Se não encontrar, procurar por normalização
        $editoras = Editora::all();
        foreach ($editoras as $editora) {
            if ($this->normalizarNome($editora->nome) === $nomeNormalizado) {
                Log::info('[NormalizaNomes] Editora encontrada por normalização', ['id' => $editora->id, 'nome' => $editora->nome]);
                return $editora;
            }
        }

        // Não encontrou, criar nova
        Log::info('[NormalizaNomes] Editora não encontrada, a criar nova', ['nome' => $nomeEditora]);
        return Editora::create(['nome' => $nomeEditora]);
    }
}
