<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivroController extends Controller
{
    public function show($id, $slug = null)
    {
        $livro = Livro::with(['editora', 'autores'])->findOrFail($id);

        // Verificar permissão
        if (!auth()->user()->canViewBooks()) {
            abort(403);
        }

        // Se não houver slug ou estiver errado, redireciona para o correto
        $correctSlug = Str::slug($livro->nome);
        if ($slug !== $correctSlug) {
            return redirect()->route('livros.show', ['id' => $livro->id, 'slug' => $correctSlug]);
        }

        return view('livros.show', compact('livro'));
    }
}
