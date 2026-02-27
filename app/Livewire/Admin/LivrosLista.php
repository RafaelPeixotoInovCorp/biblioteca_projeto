<?php

namespace App\Livewire\Admin;

use App\Models\Livro;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class LivrosLista extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['livroSaved' => '$refresh'];

    public function delete($id)
    {
        $livro = Livro::find($id);
        if ($livro) {
            $livro->delete();
            session()->flash('message', 'Livro eliminado com sucesso!');
        }
    }

    public function render()
    {
        $livrosQuery = Livro::with(['editora', 'autores']);

        if ($this->search) {
            $livrosQuery->where('nome', 'like', '%' . $this->search . '%');
        }

        // Buscar todos e ordenar em memória
        $allLivros = $livrosQuery->get()->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE)->values();

        // Paginar manualmente
        $page = $this->getPage();
        $perPage = 10;
        $livros = new LengthAwarePaginator(
            $allLivros->forPage($page, $perPage),
            $allLivros->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.admin.livros-lista', ['livros' => $livros]);
    }
}
