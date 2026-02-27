<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;

class LivrosIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filtro_autor = '';
    public $filtro_editora = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'filtro_autor', 'filtro_editora', 'sortField', 'sortDirection'];

    public function mount()
    {
        $this->filtro_autor = request()->get('filtro_autor', '');
        $this->filtro_editora = request()->get('filtro_editora', '');
        $this->search = request()->get('search', '');
        $this->sortField = request()->get('sortField', 'nome');
        $this->sortDirection = request()->get('sortDirection', 'asc');
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        if (!auth()->user()->canViewBooks()) {
            abort(403);
        }

        // Buscar todos os livros com relacionamentos
        $allLivros = Livro::with(['editora', 'autores'])->get();

        // Filtrar por pesquisa (nome do livro, autor, editora)
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $allLivros = $allLivros->filter(function($livro) use ($searchLower) {
                // Pesquisar no nome do livro
                if (str_contains(strtolower($livro->nome), $searchLower)) {
                    return true;
                }

                // Pesquisar no ISBN
                if (str_contains(strtolower($livro->isbn), $searchLower)) {
                    return true;
                }

                // Pesquisar nos autores
                foreach ($livro->autores as $autor) {
                    if (str_contains(strtolower($autor->nome), $searchLower)) {
                        return true;
                    }
                }

                // Pesquisar na editora
                if ($livro->editora && str_contains(strtolower($livro->editora->nome), $searchLower)) {
                    return true;
                }

                return false;
            });
        }

        // Filtrar por autor específico
        if ($this->filtro_autor) {
            $allLivros = $allLivros->filter(function($livro) {
                return $livro->autores->contains('id', $this->filtro_autor);
            });
        }

        // Filtrar por editora específica
        if ($this->filtro_editora) {
            $allLivros = $allLivros->filter(function($livro) {
                return $livro->editora_id == $this->filtro_editora;
            });
        }

        // Ordenar
        if ($this->sortField === 'nome') {
            $allLivros = $allLivros->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE);
        } elseif ($this->sortField === 'preco') {
            $allLivros = $allLivros->sortBy('preco');
        } elseif ($this->sortField === 'autor') {
            $allLivros = $allLivros->sortBy(function($livro) {
                return $livro->autores->first()?->nome ?? '';
            }, SORT_NATURAL|SORT_FLAG_CASE);
        }

        if ($this->sortDirection === 'desc') {
            $allLivros = $allLivros->reverse();
        }

        $allLivros = $allLivros->values();

        // Paginar
        $page = $this->getPage();
        $perPage = 12;
        $livros = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLivros->forPage($page, $perPage),
            $allLivros->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        $autores = Autor::all()->sortBy('nome')->values();
        $editoras = Editora::all()->sortBy('nome')->values();

        return view('livewire.livros-index', [
            'livros' => $livros,
            'autores' => $autores,
            'editoras' => $editoras,
            'isAdmin' => auth()->user()->isAdmin(),
            'canManage' => auth()->user()->canManageBooks(),
        ]);
    }
}
