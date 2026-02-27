<?php

namespace App\Http\Controllers\Admin;

use App\Models\Editora;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        // Buscar todas as editoras
        $allEditoras = Editora::withCount('livros')->get();

        // Filtrar por pesquisa
        if ($search) {
            $searchLower = strtolower($search);
            $allEditoras = $allEditoras->filter(function($editora) use ($searchLower) {
                return str_contains(strtolower($editora->nome), $searchLower);
            });
        }

        // Ordenar por número de livros (decrescente)
        $allEditoras = $allEditoras->sortByDesc('livros_count')->values();

        // Paginar manualmente
        $page = $request->get('page', 1);
        $perPage = 10;
        $editoras = new LengthAwarePaginator(
            $allEditoras->forPage($page, $perPage),
            $allEditoras->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.editoras', compact('editoras'));
    }

    public function create()
    {
        return view('admin.editoras-form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|min:2',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        $dados = ['nome' => $request->nome];

        if ($request->hasFile('logotipo')) {
            $dados['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        Editora::create($dados);

        return redirect()->route('admin.editoras')->with('success', 'Editora criada com sucesso!');
    }

    public function edit($id)
    {
        $editora = Editora::findOrFail($id);
        return view('admin.editoras-form', compact('editora'));
    }

    public function update(Request $request, $id)
    {
        $editora = Editora::findOrFail($id);

        $request->validate([
            'nome' => 'required|min:2',
            'logotipo' => 'nullable|image|max:2048',
        ]);

        $dados = ['nome' => $request->nome];

        if ($request->hasFile('logotipo')) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $dados['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora->update($dados);

        return redirect()->route('admin.editoras')->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $editora = Editora::findOrFail($id);

        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }

        $editora->delete();

        return redirect()->route('admin.editoras')->with('success', 'Editora eliminada com sucesso!');
    }
}
