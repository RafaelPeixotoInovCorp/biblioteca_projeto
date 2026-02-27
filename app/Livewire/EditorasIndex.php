<?php

namespace App\Livewire;

use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class EditorasIndex extends Component
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
        if (!auth()->user()->canViewPublishers()) {
            abort(403);
        }

        // Buscar todas as editoras
        $allEditoras = Editora::withCount('livros')->get();

        // Filtrar por pesquisa
        if ($this->search) {
            $searchLower = strtolower($this->search);
            $allEditoras = $allEditoras->filter(function($editora) use ($searchLower) {
                return str_contains(strtolower($editora->nome), $searchLower);
            });
        }

        // Ordenar
        if ($this->sortField === 'nome') {
            $allEditoras = $allEditoras->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE);
        } elseif ($this->sortField === 'livros') {
            $allEditoras = $allEditoras->sortByDesc('livros_count');
        }

        if ($this->sortDirection === 'desc') {
            $allEditoras = $allEditoras->reverse();
        }

        $allEditoras = $allEditoras->values();

        // Paginar
        $page = $this->getPage();
        $perPage = 12;
        $editoras = new LengthAwarePaginator(
            $allEditoras->forPage($page, $perPage),
            $allEditoras->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.editoras-index', [
            'editoras' => $editoras,
            'isAdmin' => auth()->user()->isAdmin(),
            'canManage' => auth()->user()->canManagePublishers(),
        ]);
    }
}
