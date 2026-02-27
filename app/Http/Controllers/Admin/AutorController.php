<?php

namespace App\Http\Controllers\Admin;

use App\Models\Autor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class AutorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Buscar todos os autores
        $allAutores = Autor::withCount('livros')->get();

        // Filtrar por pesquisa
        if ($search) {
            $searchLower = strtolower($search);
            $allAutores = $allAutores->filter(function($autor) use ($searchLower) {
                return str_contains(strtolower($autor->nome), $searchLower);
            });
        }

        // Ordenar por número de livros (decrescente)
        $allAutores = $allAutores->sortByDesc('livros_count')->values();

        // Paginar manualmente
        $page = $request->get('page', 1);
        $perPage = 10;
        $autores = new LengthAwarePaginator(
            $allAutores->forPage($page, $perPage),
            $allAutores->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.autores', compact('autores'));
    }

    public function create()
    {
        return view('admin.autores-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|min:3',
            'foto' => 'nullable|image|max:2048',
        ]);

        $dados = ['nome' => $request->nome];

        if ($request->hasFile('foto')) {
            $dados['foto'] = $request->file('foto')->store('autores', 'public');
        }

        Autor::create($dados);

        return redirect()->route('admin.autores')->with('success', 'Autor criado com sucesso!');
    }

    public function edit($id)
    {
        $autor = Autor::findOrFail($id);
        return view('admin.autores-form', compact('autor'));
    }

    public function update(Request $request, $id)
    {
        $autor = Autor::findOrFail($id);

        $request->validate([
            'nome' => 'required|min:3',
            'foto' => 'nullable|image|max:2048',
        ]);

        $dados = ['nome' => $request->nome];

        if ($request->hasFile('foto')) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }
            $dados['foto'] = $request->file('foto')->store('autores', 'public');
        }

        $autor->update($dados);

        return redirect()->route('admin.autores')->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $autor = Autor::findOrFail($id);

        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }

        $autor->delete();

        return redirect()->route('admin.autores')->with('success', 'Autor eliminado com sucesso!');
    }
}
