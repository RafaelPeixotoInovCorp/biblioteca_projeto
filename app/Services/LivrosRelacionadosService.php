<?php

namespace App\Services;

use App\Models\Livro;
use Illuminate\Support\Facades\Log;

class LivrosRelacionadosService
{
    /**
     * Lista de palavras a ignorar (stopwords em português)
     */
    protected $stopwords = [
        'a', 'ao', 'aos', 'aquela', 'aquelas', 'aquele', 'aqueles',
        'aquilo', 'as', 'até', 'com', 'como', 'da', 'das', 'de', 'dela',
        'delas', 'dele', 'deles', 'depois', 'do', 'dos', 'e', 'ela',
        'elas', 'ele', 'eles', 'em', 'entre', 'era', 'eram', 'essa',
        'essas', 'esse', 'esses', 'esta', 'estamos', 'estas', 'estava',
        'estavam', 'este', 'estes', 'estou', 'eu', 'foi', 'foram',
        'há', 'isso', 'isto', 'já', 'la', 'lhe', 'lhes', 'lo', 'mas',
        'me', 'mesmo', 'meu', 'meus', 'minha', 'minhas', 'muito', 'na',
        'não', 'nas', 'nem', 'no', 'nos', 'nossa', 'nossas', 'nosso',
        'nossos', 'num', 'numa', 'o', 'os', 'ou', 'para', 'pela',
        'pelas', 'pelo', 'pelos', 'por', 'qual', 'quando', 'que',
        'quem', 'se', 'seja', 'sem', 'seu', 'seus', 'sou', 'sua',
        'suas', 'também', 'te', 'tem', 'têm', 'teu', 'teus', 'ti',
        'tua', 'tuas', 'um', 'uma', 'umas', 'você', 'vocês', 'vos',
        'à', 'às', 'é', 'está', 'estão', 'foi', 'foram', 'ser', 'são',
        'livro', 'livros', 'página', 'páginas', 'capítulo', 'capítulos',
        'autor', 'autores', 'editora', 'edição', 'obra', 'obras',
        'história', 'histórias', 'conto', 'contos', 'romance', 'romances',
        'ficção', 'realidade', 'mundo', 'vida', 'tempo', 'ano', 'anos',
        'dia', 'dias', 'noite', 'noites', 'homem', 'homens', 'mulher',
        'mulheres', 'amor', 'paixão', 'morte', 'viver', 'morrer',
    ];

    /**
     * Encontra livros relacionados a um livro específico
     */
    public function encontrarRelacionados(Livro $livro, int $limite = 4): array
    {
        // Obter palavras-chave do livro atual
        $palavrasChave = $this->extrairPalavrasChave($livro);

        if (empty($palavrasChave)) {
            // Fallback: livros do mesmo autor ou editora
            return $this->fallbackRelacionados($livro, $limite);
        }

        // Buscar todos os outros livros
        $outrosLivros = Livro::with(['autores', 'editora'])
            ->where('id', '!=', $livro->id)
            ->get();

        // Calcular pontuação de similaridade para cada livro
        $pontuacoes = [];
        foreach ($outrosLivros as $outroLivro) {
            $pontuacao = $this->calcularSimilaridade($palavrasChave, $outroLivro);
            if ($pontuacao > 0) {
                $pontuacoes[$outroLivro->id] = [
                    'livro' => $outroLivro,
                    'pontuacao' => $pontuacao,
                ];
            }
        }

        // Ordenar por pontuação (maior para menor)
        usort($pontuacoes, function($a, $b) {
            return $b['pontuacao'] <=> $a['pontuacao'];
        });

        // Retornar apenas os livros (sem as pontuações)
        $relacionados = array_slice(array_column($pontuacoes, 'livro'), 0, $limite);

        // Se não encontrou relacionados por palavras, usa fallback
        if (empty($relacionados)) {
            return $this->fallbackRelacionados($livro, $limite);
        }

        return $relacionados;
    }

    /**
     * Extrai palavras-chave relevantes da descrição do livro
     */
    protected function extrairPalavrasChave(Livro $livro): array
    {
        $texto = '';

        // Adicionar título (com peso maior)
        $texto .= ' ' . $livro->nome;

        // Adicionar nomes dos autores
        foreach ($livro->autores as $autor) {
            $texto .= ' ' . $autor->nome;
        }

        // Adicionar nome da editora
        if ($livro->editora) {
            $texto .= ' ' . $livro->editora->nome;
        }

        // Adicionar bibliografia se existir
        if ($livro->bibliografia) {
            $texto .= ' ' . $livro->bibliografia;
        }

        // Normalizar texto
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = $this->removerAcentos($texto);
        $texto = preg_replace('/[^a-z0-9\s]/', ' ', $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        // Dividir em palavras
        $palavras = explode(' ', trim($texto));

        // Filtrar stopwords e palavras curtas
        $palavras = array_filter($palavras, function($palavra) {
            return strlen($palavra) > 3 && !in_array($palavra, $this->stopwords);
        });

        // Contar frequência das palavras
        $frequencias = array_count_values($palavras);

        // Ordenar por frequência (mais frequentes primeiro)
        arsort($frequencias);

        // Retornar apenas as palavras (sem frequências)
        return array_keys($frequencias);
    }

    /**
     * Calcula similaridade entre palavras-chave e outro livro
     */
    protected function calcularSimilaridade(array $palavrasChave, Livro $outroLivro): float
    {
        $pontuacao = 0;

        // Extrair palavras do outro livro
        $outrasPalavras = $this->extrairPalavrasChave($outroLivro);

        // Calcular interseção de palavras
        $comuns = array_intersect($palavrasChave, $outrasPalavras);

        // Pontuação baseada em quantas palavras comuns
        $pontuacao += count($comuns) * 10;

        // Bónus por palavras muito específicas (mais raras)
        foreach ($comuns as $palavra) {
            // Palavras mais longas têm mais peso
            $pontuacao += strlen($palavra) * 2;
        }

        // Bónus por mesmo autor
        foreach ($outroLivro->autores as $autor) {
            foreach ($palavrasChave as $palavra) {
                if (stripos($autor->nome, $palavra) !== false) {
                    $pontuacao += 50;
                    break;
                }
            }
        }

        // Bónus por mesma editora
        if ($outroLivro->editora) {
            foreach ($palavrasChave as $palavra) {
                if (stripos($outroLivro->editora->nome, $palavra) !== false) {
                    $pontuacao += 30;
                    break;
                }
            }
        }

        return $pontuacao;
    }

    /**
     * Fallback: livros do mesmo autor ou editora
     */
    protected function fallbackRelacionados(Livro $livro, int $limite): array
    {
        $relacionados = collect();

        // Primeiro, livros do mesmo autor
        foreach ($livro->autores as $autor) {
            $livrosDoAutor = Livro::whereHas('autores', function($q) use ($autor) {
                $q->where('autor_id', $autor->id);
            })
                ->where('id', '!=', $livro->id)
                ->limit($limite)
                ->get();

            $relacionados = $relacionados->merge($livrosDoAutor);
        }

        // Se ainda não chegar ao limite, livros da mesma editora
        if ($relacionados->count() < $limite && $livro->editora) {
            $livrosDaEditora = Livro::where('editora_id', $livro->editora_id)
                ->where('id', '!=', $livro->id)
                ->whereNotIn('id', $relacionados->pluck('id'))
                ->limit($limite - $relacionados->count())
                ->get();

            $relacionados = $relacionados->merge($livrosDaEditora);
        }

        // Se ainda não chegar ao limite, livros aleatórios
        if ($relacionados->count() < $limite) {
            $aleatorios = Livro::where('id', '!=', $livro->id)
                ->whereNotIn('id', $relacionados->pluck('id'))
                ->inRandomOrder()
                ->limit($limite - $relacionados->count())
                ->get();

            $relacionados = $relacionados->merge($aleatorios);
        }

        return $relacionados->take($limite)->values()->all();
    }

    /**
     * Remove acentos de uma string
     */
    protected function removerAcentos(string $texto): string
    {
        $acentos = [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N'
        ];

        return strtr($texto, $acentos);
    }
}
