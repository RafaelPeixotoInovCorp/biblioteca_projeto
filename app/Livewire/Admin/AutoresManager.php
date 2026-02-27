<?php

namespace App\Livewire\Admin;

use App\Models\Autor;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class AutoresManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $autor_id;
    public $nome;
    public $foto;
    public $nova_foto;
    public $showModal = false;
    public $modalTitle = 'Novo Autor';
    public $editMode = false;

    protected $rules = [
        'nome' => 'required|min:3',
        'nova_foto' => 'nullable|image|max:2048',
    ];

    protected $listeners = ['deleteAutor'];

    public function create()
    {
        $this->resetValidation();
        $this->reset(['autor_id', 'nome', 'foto', 'nova_foto']);
        $this->modalTitle = 'Novo Autor';
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $autor = Autor::find($id);

        $this->autor_id = $autor->id;
        $this->nome = $autor->nome;
        $this->foto = $autor->foto;

        $this->modalTitle = 'Editar Autor';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->nova_foto) {
            $caminho_foto = $this->nova_foto->store('autores', 'public');
        }

        $dados = ['nome' => $this->nome];

        if (isset($caminho_foto)) {
            $dados['foto'] = $caminho_foto;
        }

        if ($this->editMode) {
            $autor = Autor::find($this->autor_id);
            if ($this->nova_foto && $autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }
            $autor->update($dados);
            session()->flash('message', 'Autor atualizado com sucesso!');
        } else {
            Autor::create($dados);
            session()->flash('message', 'Autor criado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['autor_id', 'nome', 'foto', 'nova_foto']);
    }

    public function delete($id)
    {
        $autor = Autor::find($id);
        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }
        $autor->delete();
        session()->flash('message', 'Autor eliminado com sucesso!');
    }

    public function render()
    {
        $autoresQuery = Autor::withCount('livros');

        if ($this->search) {
            $autoresQuery->where('nome', 'like', '%' . $this->search . '%');
        }

        // Buscar todos e ordenar por número de livros (decrescente)
        $allAutores = $autoresQuery->get()->sortByDesc('livros_count')->values();

        // Paginar manualmente
        $page = $this->getPage();
        $perPage = 10;
        $autores = new LengthAwarePaginator(
            $allAutores->forPage($page, $perPage),
            $allAutores->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.admin.autores-manager', ['autores' => $autores]);
    }
}
