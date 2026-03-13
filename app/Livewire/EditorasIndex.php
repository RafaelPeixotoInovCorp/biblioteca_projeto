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

    /**
     * Método para eliminar editoras (apenas para admin)
     */
    public function delete($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $editora = Editora::find($id);
        if ($editora) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $editora->delete();
            session()->flash('message', 'Editora eliminada com sucesso!');
        }
    }

    public function render()
    {
        if (!auth()->user()->canViewPublishers()) {
            abort(403);
        }

        $allEditoras = Editora::withCount('livros')->get();

        if ($this->search) {
            $searchLower = strtolower($this->search);
            $allEditoras = $allEditoras->filter(function($editora) use ($searchLower) {
                return str_contains(strtolower($editora->nome), $searchLower);
            });
        }

        if ($this->sortField === 'nome') {
            $allEditoras = $allEditoras->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE);
        } elseif ($this->sortField === 'livros') {
            $allEditoras = $allEditoras->sortByDesc('livros_count');
        }

        if ($this->sortDirection === 'desc') {
            $allEditoras = $allEditoras->reverse();
        }

        $allEditoras = $allEditoras->values();

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
