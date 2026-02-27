<?php

namespace App\Livewire\Admin;

use App\Models\Editora;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class EditorasManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
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
            $editora = Editora::find($this->editora_id);
            if ($this->novo_logotipo && $editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $editora->update($dados);
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
        $editora = Editora::find($id);
        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }
        $editora->delete();
        session()->flash('message', 'Editora eliminada com sucesso!');
    }

    public function render()
    {
        $editorasQuery = Editora::withCount('livros');

        if ($this->search) {
            $editorasQuery->where('nome', 'like', '%' . $this->search . '%');
        }

        // Buscar todas e ordenar por número de livros (decrescente)
        $allEditoras = $editorasQuery->get()->sortByDesc('livros_count')->values();

        // Paginar manualmente
        $page = $this->getPage();
        $perPage = 10;
        $editoras = new LengthAwarePaginator(
            $allEditoras->forPage($page, $perPage),
            $allEditoras->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('livewire.admin.editoras-manager', ['editoras' => $editoras]);
    }
}
