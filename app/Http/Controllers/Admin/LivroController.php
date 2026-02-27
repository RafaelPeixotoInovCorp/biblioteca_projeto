<?php

namespace App\Http\Controllers\Admin;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class LivroController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $searchType = $request->get('type', 'tudo');

        // Buscar todos os livros
        $allLivros = Livro::with(['editora', 'autores'])->get();

        // Filtrar por pesquisa
        if ($search) {
            $searchLower = strtolower($search);
            $allLivros = $allLivros->filter(function($livro) use ($searchLower, $searchType) {
                if ($searchType === 'isbn') {
                    return str_contains(strtolower($livro->isbn), $searchLower);
                } elseif ($searchType === 'titulo') {
                    return str_contains(strtolower($livro->nome), $searchLower);
                } elseif ($searchType === 'autor') {
                    foreach ($livro->autores as $autor) {
                        if (str_contains(strtolower($autor->nome), $searchLower)) {
                            return true;
                        }
                    }
                    return false;
                } elseif ($searchType === 'editora') {
                    return $livro->editora && str_contains(strtolower($livro->editora->nome), $searchLower);
                } else {
                    // Pesquisa em tudo
                    if (str_contains(strtolower($livro->isbn), $searchLower)) return true;
                    if (str_contains(strtolower($livro->nome), $searchLower)) return true;
                    foreach ($livro->autores as $autor) {
                        if (str_contains(strtolower($autor->nome), $searchLower)) return true;
                    }
                    if ($livro->editora && str_contains(strtolower($livro->editora->nome), $searchLower)) return true;
                    return false;
                }
            });
        }

        // Ordenar por nome
        $allLivros = $allLivros->sortBy('nome', SORT_NATURAL|SORT_FLAG_CASE)->values();

        // Paginar manualmente
        $page = $request->get('page', 1);
        $perPage = 10;
        $livros = new LengthAwarePaginator(
            $allLivros->forPage($page, $perPage),
            $allLivros->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.livros', compact('livros'));
    }

    // ... restante do controller

    public function create()
    {
        $editoras = Editora::all()->sortBy('nome');
        $autores = Autor::all()->sortBy('nome');
        return view('admin.livros-form', compact('editoras', 'autores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'isbn' => 'required|unique:livros,isbn',
            'nome' => 'required|min:3',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autors,id', // Agora é autor_id único
            'preco' => 'required|numeric|min:0',
            'imagem_capa' => 'nullable|image|max:2048',
        ]);

        $dados = $request->except(['imagem_capa']);

        if ($request->hasFile('imagem_capa')) {
            $dados['imagem_capa'] = $request->file('imagem_capa')->store('capas', 'public');
        }

        $livro = Livro::create($dados);

        // Associar o autor (agora é apenas um)
        $livro->autores()->attach($request->autor_id);

        return redirect()->route('admin.livros')->with('success', 'Livro criado com sucesso!');
    }

    public function edit($id)
    {
        $livro = Livro::with('autores')->findOrFail($id);
        $editoras = Editora::all()->sortBy('nome');
        $autores = Autor::all()->sortBy('nome');
        $livroAutores = $livro->autores->pluck('id')->toArray();

        return view('admin.livros-form', compact('livro', 'editoras', 'autores', 'livroAutores'));
    }

    public function update(Request $request, $id)
    {
        $livro = Livro::findOrFail($id);

        $request->validate([
            'isbn' => 'required|unique:livros,isbn,' . $id,
            'nome' => 'required|min:3',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autors,id', // Agora é autor_id único
            'preco' => 'required|numeric|min:0',
            'imagem_capa' => 'nullable|image|max:2048',
        ]);

        $dados = $request->except(['imagem_capa']);

        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }
            $dados['imagem_capa'] = $request->file('imagem_capa')->store('capas', 'public');
        }

        $livro->update($dados);
        $livro->autores()->sync([$request->autor_id]);

        return redirect()->route('admin.livros')->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $livro = Livro::findOrFail($id);

        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }

        $livro->delete();

        return redirect()->route('admin.livros')->with('success', 'Livro eliminado com sucesso!');
    }
}
