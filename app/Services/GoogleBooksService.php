<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksService
{
    protected $apiKey;
    protected $baseUrl = 'https://www.googleapis.com/books/v1/';

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_BOOKS_API_KEY');
    }

    /**
     * Pesquisa livros na Google Books API.
     */
    public function searchBooks(string $query, int $maxResults = 20): ?array
    {
        $url = $this->baseUrl . 'volumes';

        $this->logInfo('A pesquisar livros', ['query' => $query, 'maxResults' => $maxResults]);

        try {
            $params = [
                'q' => $query,
                'maxResults' => $maxResults,
                'printType' => 'books',
            ];

            // Adicionar langRestrict apenas se não estiver vazio
            if (!empty($query)) {
                $params['langRestrict'] = 'pt';
            }

            // Adicionar API key se existir (opcional)
            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $this->logInfo('Parâmetros da requisição', $params);

            // Aumentar timeout e ignorar SSL em desenvolvimento
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($url, $params);

            $this->logInfo('Resposta recebida', [
                'status' => $response->status(),
                'headers' => $response->headers()
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['items']) && is_array($data['items'])) {
                    $this->logInfo('Livros encontrados', ['count' => count($data['items'])]);
                    return $data['items'];
                } else {
                    $this->logWarning('Resposta sem livros', ['data' => $data]);
                    return [];
                }
            }

            // Log detalhado do erro
            $this->logError('Google Books API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            return null;

        } catch (\Exception $e) {
            $this->logError('Google Books API exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * Obtém um livro específico pelo ID.
     */
    public function getBook(string $volumeId): ?array
    {
        $url = $this->baseUrl . 'volumes/' . $volumeId;

        try {
            $params = [];
            if ($this->apiKey) {
                $params['key'] = $this->apiKey;
            }

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get($url, $params);

            if ($response->successful()) {
                return $response->json();
            }

            $this->logError('Google Books API error (getBook)', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;

        } catch (\Exception $e) {
            $this->logError('Google Books API exception (getBook)', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Mapeia os dados da API para o formato da base de dados.
     */
    public function mapToDatabase(array $apiBookData): array
    {
        $volumeInfo = $apiBookData['volumeInfo'] ?? [];

        $autores = $volumeInfo['authors'] ?? ['Autor Desconhecido'];
        $editora = $volumeInfo['publisher'] ?? 'Editora Desconhecida';

        $isbn = null;
        $industryIdentifiers = $volumeInfo['industryIdentifiers'] ?? [];
        foreach ($industryIdentifiers as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                $isbn = $identifier['identifier'];
                break;
            }
        }
        if (!$isbn) {
            foreach ($industryIdentifiers as $identifier) {
                if ($identifier['type'] === 'ISBN_10') {
                    $isbn = $identifier['identifier'];
                    break;
                }
            }
        }

        $imageLinks = $volumeInfo['imageLinks'] ?? [];
        $imagemCapa = $imageLinks['thumbnail'] ?? null;
        if ($imagemCapa) {
            $imagemCapa = preg_replace('/&zoom=\d/', '&zoom=2', $imagemCapa);
        }

        $dataPublicacao = $volumeInfo['publishedDate'] ?? null;
        $paginas = $volumeInfo['pageCount'] ?? null;
        $categorias = $volumeInfo['categories'] ?? [];

        return [
            'isbn' => $isbn,
            'titulo' => $volumeInfo['title'] ?? 'Título Desconhecido',
            'subtitulo' => $volumeInfo['subtitle'] ?? null,
            'autores' => $autores,
            'editora' => $editora,
            'descricao' => $volumeInfo['description'] ?? null,
            'imagem_capa_url' => $imagemCapa,
            'data_publicacao' => $dataPublicacao,
            'paginas' => $paginas,
            'categorias' => $categorias,
            'idioma' => $volumeInfo['language'] ?? null,
        ];
    }

    private function logInfo($message, $context = [])
    {
        Log::info('[GoogleBooksService] ' . $message, $context);
    }

    private function logWarning($message, $context = [])
    {
        Log::warning('[GoogleBooksService] ' . $message, $context);
    }

    private function logError($message, $context = [])
    {
        Log::error('[GoogleBooksService] ' . $message, $context);
    }
}
