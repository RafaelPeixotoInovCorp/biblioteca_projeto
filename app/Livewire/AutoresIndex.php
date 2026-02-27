<?php

namespace App\Livewire;

use App\Models\Autor;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class AutoresIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'nome';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

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
        if (!auth()->user()->canViewAuthors()) {
            abort(403);
        }

        // Buscar todos os autores
        $allAutores = Autor::with('livros')->get();

        // Filtrar por pesquisa
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $allAutores = $allAutores->filter(function($autor) use ($searchLower) {
                return str_contains(strtolower($autor->nome), $searchLower);
            });
        }

        // Ordenar
        if ($this->sortField === 'nome') {
            $allAutores = $allAutores->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE);
        } elseif ($this->sortField === 'livros') {
            $allAutores = $allAutores->sortByDesc(function($autor) {
                return $autor->livros->count();
            });
        }

        if ($this->sortDirection === 'desc') {
            $allAutores = $allAutores->reverse();
        }

        $allAutores = $allAutores->values();

        // Paginar
        $page = $this->getPage();
        $perPage = 12;
        $autores = new LengthAwarePaginator(
            $allAutores->forPage($page, $perPage),
            $allAutores->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.autores-index', [
            'autores' => $autores,
            'isAdmin' => auth()->user()->isAdmin(),
            'canManage' => auth()->user()->canManageAuthors(),
        ]);
    }
}
