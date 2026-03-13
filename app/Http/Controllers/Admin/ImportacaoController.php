<?php

namespace App\Http\Controllers\Admin;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Services\GoogleBooksService;
use App\Traits\NormalizaNomes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportacaoController extends Controller
{
    use NormalizaNomes;

    protected $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    /**
     * Mostra a página de pesquisa
     */
    public function index()
    {
        return view('admin.importar.index');
    }

    /**
     * Pesquisa livros na Google Books API
     */
    public function pesquisar(Request $request)
    {
        $request->validate([
            'query' => 'required|min:3',
        ]);

        $query = $request->input('query');
        $maxResults = $request->input('max_results', 20);

        Log::info('[ImportacaoController] A iniciar pesquisa', [
            'query' => $query,
            'maxResults' => $maxResults
        ]);

        try {
            $resultados = $this->googleBooksService->searchBooks($query, $maxResults);

            Log::info('[ImportacaoController] Resultado da pesquisa', [
                'resultados' => $resultados ? count($resultados) : 'null'
            ]);

            if ($resultados === null) {
                Log::error('[ImportacaoController] API retornou null');
                return back()->with('error', 'Erro na comunicação com a Google Books API. Verifique os logs.');
            }

            if (empty($resultados)) {
                Log::info('[ImportacaoController] Nenhum resultado encontrado');
                return back()->with('warning', 'Nenhum livro encontrado para "' . $query . '"');
            }

            // Processar resultados para a view
            $livros = [];
            foreach ($resultados as $item) {
                $dados = $this->googleBooksService->mapToDatabase($item);
                $dados['google_id'] = $item['id'];
                $dados['ja_existe'] = $this->livroJaExiste($dados);
                $livros[] = $dados;
            }

            Log::info('[ImportacaoController] Processamento concluído', [
                'livros_processados' => count($livros)
            ]);

            return view('admin.importar.resultados', [
                'livros' => $livros,
                'query' => $query,
                'total' => count($livros)
            ]);

        } catch (\Exception $e) {
            Log::error('[ImportacaoController] Exceção na importação: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Erro ao pesquisar livros: ' . $e->getMessage());
        }
    }

    /**
     * Importa um livro específico
     */
    public function importar(Request $request)
    {
        $request->validate([
            'google_id' => 'required|string',
        ]);

        try {
            $livroDetalhes = $this->googleBooksService->getBook($request->google_id);

            if (!$livroDetalhes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livro não encontrado na API'
                ], 404);
            }

            $dados = $this->googleBooksService->mapToDatabase($livroDetalhes);

            // Verificar se já existe
            if ($this->livroJaExiste($dados)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este livro já existe na base de dados'
                ], 409);
            }

            // Encontrar ou criar editora (com normalização)
            $editora = $this->encontrarOuCriarEditora($dados['editora']);

            // Criar livro
            $livro = Livro::create([
                'isbn' => $dados['isbn'],
                'nome' => $dados['titulo'],
                'editora_id' => $editora->id,
                'bibliografia' => $dados['descricao'],
                'preco' => $dados['preco'] ?? null,
                'disponivel' => true,
            ]);

            // Encontrar ou criar autores (com normalização)
            foreach ($dados['autores'] as $nomeAutor) {
                $autor = $this->encontrarOuCriarAutor($nomeAutor);
                if ($autor) {
                    $livro->autores()->attach($autor->id);
                }
            }

            // Descarregar imagem (se existir)
            if ($dados['imagem_capa_url']) {
                $this->descarregarImagem($livro, $dados['imagem_capa_url']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Livro importado com sucesso!',
                'livro' => [
                    'id' => $livro->id,
                    'nome' => $livro->nome,
                    'isbn' => $livro->isbn,
                    'editora' => $editora->nome,
                    'autores' => $livro->autores->pluck('nome')->toArray()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao importar livro: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao importar livro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importa múltiplos livros de uma vez
     */
    public function importarMultiplos(Request $request)
    {
        $request->validate([
            'google_ids' => 'required|array',
            'google_ids.*' => 'required|string',
        ]);

        $resultados = [
            'sucesso' => [],
            'erro' => [],
            'existentes' => []
        ];

        foreach ($request->google_ids as $googleId) {
            try {
                $livroDetalhes = $this->googleBooksService->getBook($googleId);

                if (!$livroDetalhes) {
                    $resultados['erro'][] = "ID {$googleId}: Livro não encontrado";
                    continue;
                }

                $dados = $this->googleBooksService->mapToDatabase($livroDetalhes);

                // Verificar se já existe
                if ($this->livroJaExiste($dados)) {
                    $resultados['existentes'][] = $dados['titulo'];
                    continue;
                }

                // Encontrar ou criar editora
                $editora = $this->encontrarOuCriarEditora($dados['editora']);

                // Criar livro
                $livro = Livro::create([
                    'isbn' => $dados['isbn'],
                    'nome' => $dados['titulo'],
                    'editora_id' => $editora->id,
                    'bibliografia' => $dados['descricao'],
                    'preco' => $dados['preco'] ?? null,
                    'disponivel' => true,
                ]);

                // Encontrar ou criar autores
                foreach ($dados['autores'] as $nomeAutor) {
                    $autor = $this->encontrarOuCriarAutor($nomeAutor);
                    if ($autor) {
                        $livro->autores()->attach($autor->id);
                    }
                }

                if ($dados['imagem_capa_url']) {
                    $this->descarregarImagem($livro, $dados['imagem_capa_url']);
                }

                $resultados['sucesso'][] = $livro->nome;

            } catch (\Exception $e) {
                $resultados['erro'][] = "ID {$googleId}: " . $e->getMessage();
            }
        }

        return response()->json($resultados);
    }
/**
* Verifica se o livro já existe na base de dados
*/
    private function livroJaExiste(array $dados): bool
    {
        // Verificar por ISBN primeiro
        if (!empty($dados['isbn'])) {
            $existe = Livro::where('isbn', $dados['isbn'])->exists();
            if ($existe) {
                Log::info('[livroJaExiste] Livro encontrado por ISBN', ['isbn' => $dados['isbn']]);
                return true;
            }
        }

        // Verificar por título + autor principal
        if (!empty($dados['titulo']) && !empty($dados['autores'][0])) {
            $existe = Livro::where('nome', $dados['titulo'])
                ->whereHas('autores', function ($q) use ($dados) {
                    $q->where('nome', $dados['autores'][0]);
                })
                ->exists();

            if ($existe) {
                Log::info('[livroJaExiste] Livro encontrado por título e autor', [
                    'titulo' => $dados['titulo'],
                    'autor' => $dados['autores'][0]
                ]);
                return true;
            }
        }

        Log::info('[livroJaExiste] Livro não existe', ['titulo' => $dados['titulo']]);
        return false;
    }

    /**
     * Descarrega e guarda a imagem do livro
     */
    private function descarregarImagem(Livro $livro, string $url): void
    {
        try {
            $conteudoImagem = file_get_contents($url);
            if ($conteudoImagem) {
                $nomeArquivo = 'capas/google_' . uniqid() . '.jpg';
                Storage::disk('public')->put($nomeArquivo, $conteudoImagem);
                $livro->imagem_capa = $nomeArquivo;
                $livro->save();
            }
        } catch (\Exception $e) {
            Log::warning('Não foi possível descarregar imagem: ' . $e->getMessage());
        }
    }
}
