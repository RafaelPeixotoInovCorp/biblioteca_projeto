<?php

namespace App\Console\Commands;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Services\GoogleBooksService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportarLivrosGoogleBooks extends Command
{
    protected $signature = 'livros:importar-google-books {termos* : Termos para pesquisa (ex: "josé saramago")}';
    protected $description = 'Importa livros da Google Books API baseado em termos de pesquisa';

    protected $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        parent::__construct();
        $this->googleBooksService = $googleBooksService;
    }

    public function handle()
    {
        $termos = $this->argument('termos');
        $query = implode(' ', $termos);

        $this->info("📚 A pesquisar por: {$query}");
        $this->newLine();

        // Pesquisar na API
        $livrosAPI = $this->googleBooksService->searchBooks($query, 20);

        if (empty($livrosAPI)) {
            $this->error('❌ Nenhum livro encontrado na API.');
            return 1;
        }

        $this->info("✅ Livros encontrados: " . count($livrosAPI));
        $this->newLine();

        $bar = $this->output->createProgressBar(count($livrosAPI));
        $bar->start();

        $importados = 0;
        $ignorados = 0;

        foreach ($livrosAPI as $item) {
            $dados = $this->googleBooksService->mapToDatabase($item);

            // Verificar se tem ISBN
            if (empty($dados['isbn'])) {
                $ignorados++;
                $bar->advance();
                continue;
            }

            // Verificar se livro já existe
            $livroExistente = Livro::where('isbn', $dados['isbn'])->first();
            if ($livroExistente) {
                $ignorados++;
                $bar->advance();
                continue;
            }

            // Criar ou obter Editora
            $editora = Editora::firstOrCreate(
                ['nome' => $dados['editora']]
            );

            // Criar livro
            $livro = Livro::create([
                'isbn' => $dados['isbn'],
                'nome' => $dados['titulo'],
                'editora_id' => $editora->id,
                'bibliografia' => $dados['descricao'],
                'preco' => 0, // Preço padrão (pode ser editado depois)
                'disponivel' => true,
            ]);

            // Adicionar autores
            foreach ($dados['autores'] as $nomeAutor) {
                $autor = Autor::firstOrCreate(['nome' => $nomeAutor]);
                $livro->autores()->attach($autor->id);
            }

            // Descarregar imagem (se existir)
            if ($dados['imagem_capa_url']) {
                try {
                    $conteudoImagem = file_get_contents($dados['imagem_capa_url']);
                    if ($conteudoImagem) {
                        $nomeArquivo = 'capas/google_' . uniqid() . '.jpg';
                        Storage::disk('public')->put($nomeArquivo, $conteudoImagem);
                        $livro->imagem_capa = $nomeArquivo;
                        $livro->save();
                    }
                } catch (\Exception $e) {
                    $this->warn("  ⚠ Não foi possível descarregar imagem para: {$dados['titulo']}");
                }
            }

            $importados++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Importação concluída!");
        $this->table(
            ['Status', 'Quantidade'],
            [
                ['Importados', $importados],
                ['Ignorados (sem ISBN ou já existentes)', $ignorados],
            ]
        );

        return 0;
    }
}
