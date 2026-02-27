<?php

namespace App\Livewire;

use App\Models\Autor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class AutoresTable extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

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

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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
            Autor::find($this->autor_id)->update($dados);
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
        Autor::find($id)->delete();
        session()->flash('message', 'Autor eliminado com sucesso!');
    }

    public function render()
    {
        if (!auth()->user()->canManageBooks()) {
            abort(403, 'Não tem permissão para gerir livros.');
        }

        $autores = Autor::whereRaw("cast(aes_decrypt(nome, '".env('APP_KEY')."') as char) like ?", ['%' . $this->search . '%'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.autores-table', compact('autores'));
    }
}
