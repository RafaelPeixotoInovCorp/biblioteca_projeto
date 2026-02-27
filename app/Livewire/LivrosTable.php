<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LivrosExport;

class LivrosTable extends Component
{
    use WithPagination, WithFileUploads;

    // Filtros e pesquisa
    public $search = '';
    public $filtro_editora = '';
    public $filtro_autor = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Formulário
    public $livro_id;
    public $isbn;
    public $nome;
    public $editora_id;
    public $autores = [];
    public $bibliografia;
    public $imagem_capa;
    public $preco;

    // Upload
    public $nova_imagem;

    // Modal
    public $showModal = false;
    public $modalTitle = 'Novo Livro';
    public $editMode = false;

    protected $rules = [
        'isbn' => 'required|unique:livros,isbn',
        'nome' => 'required|min:3',
        'editora_id' => 'required|exists:editoras,id',
        'autores' => 'required|array|min:1',
        'bibliografia' => 'nullable',
        'preco' => 'required|numeric|min:0',
        'nova_imagem' => 'nullable|image|max:2048', // 2MB max
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
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

    public function create()
    {
        $this->resetValidation();
        $this->reset(['livro_id', 'isbn', 'nome', 'editora_id', 'autores', 'bibliografia', 'preco', 'nova_imagem']);
        $this->modalTitle = 'Novo Livro';
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $livro = Livro::with('autores')->find($id);

        $this->livro_id = $livro->id;
        $this->isbn = $livro->isbn;
        $this->nome = $livro->nome;
        $this->editora_id = $livro->editora_id;
        $this->autores = $livro->autores->pluck('id')->toArray();
        $this->bibliografia = $livro->bibliografia;
        $this->preco = $livro->preco;
        $this->imagem_capa = $livro->imagem_capa;

        $this->modalTitle = 'Editar Livro';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editMode) {
            $this->rules['isbn'] = 'required|unique:livros,isbn,' . $this->livro_id;
        }

        $this->validate();

        if ($this->nova_imagem) {
            $caminho_imagem = $this->nova_imagem->store('capas', 'public');
        }

        $dados = [
            'isbn' => $this->isbn,
            'nome' => $this->nome,
            'editora_id' => $this->editora_id,
            'bibliografia' => $this->bibliografia,
            'preco' => $this->preco,
        ];

        if (isset($caminho_imagem)) {
            $dados['imagem_capa'] = $caminho_imagem;
        }

        if ($this->editMode) {
            $livro = Livro::find($this->livro_id);
            $livro->update($dados);
            $livro->autores()->sync($this->autores);
            session()->flash('message', 'Livro atualizado com sucesso!');
        } else {
            $livro = Livro::create($dados);
            $livro->autores()->attach($this->autores);
            session()->flash('message', 'Livro criado com sucesso!');
        }

        $this->showModal = false;
        $this->reset(['livro_id', 'isbn', 'nome', 'editora_id', 'autores', 'bibliografia', 'preco', 'nova_imagem']);
    }

    public function delete($id)
    {
        Livro::find($id)->delete();
        session()->flash('message', 'Livro eliminado com sucesso!');
    }

    public function export()
    {
        return Excel::download(new LivrosExport, 'livros.xlsx');
    }

    public function render()
    {
        if (!auth()->user()->canManageBooks()) {
            abort(403, 'Não tem permissão para gerir livros.');
        }

        $livros = Livro::with(['editora', 'autores'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('isbn', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("cast(aes_decrypt(nome, '".env('APP_KEY')."') as char) like ?", ['%' . $this->search . '%']);
                });
            })
            ->when($this->filtro_editora, function ($query) {
                $query->where('editora_id', $this->filtro_editora);
            })
            ->when($this->filtro_autor, function ($query) {
                $query->whereHas('autores', function ($q) {
                    $q->where('autors.id', $this->filtro_autor);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $editoras = Editora::all();
        $autores = Autor::all();

        return view('livewire.livros-table', compact('livros', 'editoras', 'autores'));
    }
}
