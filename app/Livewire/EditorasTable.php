<?php

namespace App\Livewire;

use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class EditorasTable extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $editora_id;
    public $nome;
    public $logotipo;
    public $novo_logotipo;

    public $showModal = false;
    public $modalTitle = 'Nova Editora';
    public $editMode = false;

    protected $rules = [
        'nome' => 'required|min:2',
        'novo_logotipo' => 'nullable|image|max:2048',
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
        $this->reset(['editora_id', 'nome', 'logotipo', 'novo_logotipo']);
        $this->modalTitle = 'Nova Editora';
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $editora = Editora::find($id);

        $this->editora_id = $editora->id;
        $this->nome = $editora->nome;
        $this->logotipo = $editora->logotipo;

        $this->modalTitle = 'Editar Editora';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->novo_logotipo) {
            $caminho_logo = $this->novo_logotipo->store('editoras', 'public');
        }

        $dados = ['nome' => $this->nome];

        if (isset($caminho_logo)) {
            $dados['logotipo'] = $caminho_logo;
        }

        if ($this->editMode) {
            Editora::find($this->editora_id)->update($dados);
            session()->flash('message', 'Editora atualizada com sucesso!');
        } else {
            Editora::create($dados);
            session()->flash('message', 'Editora criada com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['editora_id', 'nome', 'logotipo', 'novo_logotipo']);
    }

    public function delete($id)
    {
        Editora::find($id)->delete();
        session()->flash('message', 'Editora eliminada com sucesso!');
    }

    public function render()
    {

        if (!auth()->user()->canManageBooks()) {
            abort(403, 'Não tem permissão para gerir livros.');
        }

        // ... resto do código


        $editoras = Editora::whereRaw("cast(aes_decrypt(nome, '".env('APP_KEY')."') as char) like ?", ['%' . $this->search . '%'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.editoras-table', compact('editoras'));
    }
}
