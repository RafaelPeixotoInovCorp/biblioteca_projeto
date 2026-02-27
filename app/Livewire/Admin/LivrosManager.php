<?php

namespace App\Livewire\Admin;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class LivrosManager extends Component
{
    use WithFileUploads;

    public $livro_id;
    public $isbn;
    public $nome;
    public $editora_id;
    public $autores = [];
    public $bibliografia;
    public $preco;
    public $imagem_capa;
    public $nova_imagem;

    public $showModal = false;
    public $modalTitle = 'Novo Livro';
    public $editMode = false;

    protected $rules = [
        'isbn' => 'required|unique:livros,isbn',
        'nome' => 'required|min:3',
        'editora_id' => 'required|exists:editoras,id',
        'autores' => 'required|array|min:1',
        'preco' => 'required|numeric|min:0',
        'nova_imagem' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'isbn.required' => 'O ISBN é obrigatório',
        'isbn.unique' => 'Este ISBN já existe',
        'nome.required' => 'O nome é obrigatório',
        'nome.min' => 'O nome deve ter pelo menos 3 caracteres',
        'editora_id.required' => 'Selecione uma editora',
        'autores.required' => 'Selecione pelo menos um autor',
        'preco.required' => 'O preço é obrigatório',
        'preco.numeric' => 'O preço deve ser um número',
    ];

    public function mount($livroId = null)
    {
        if ($livroId) {
            $this->edit($livroId);
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['livro_id', 'isbn', 'nome', 'editora_id', 'autores', 'bibliografia', 'preco', 'nova_imagem', 'imagem_capa']);
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
            if ($this->nova_imagem && $livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }
            $livro->update($dados);
            $livro->autores()->sync($this->autores);
            session()->flash('message', 'Livro atualizado com sucesso!');
        } else {
            $livro = Livro::create($dados);
            $livro->autores()->attach($this->autores);
            session()->flash('message', 'Livro criado com sucesso!');
        }

        return redirect()->route('admin.livros');
    }

    public function delete($id)
    {
        $livro = Livro::find($id);
        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }
        $livro->delete();
        session()->flash('message', 'Livro eliminado com sucesso!');
    }

    public function render()
    {
        $editoras = Editora::all();
        $autores = Autor::all();

        return view('livewire.admin.livros-manager', [
            'editoras' => $editoras,
            'autores' => $autores,
        ]);
    }
}
