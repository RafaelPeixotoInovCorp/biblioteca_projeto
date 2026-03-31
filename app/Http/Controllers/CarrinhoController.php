<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\ItemCarrinho;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CarrinhoController extends Controller
{
    public function index()
    {
        $carrinho = $this->getCarrinho();
        return view('carrinho.index', compact('carrinho'));
    }

    public function adicionar(Request $request, Livro $livro)
    {
        $carrinho = $this->getCarrinho();

        $item = $carrinho->itens()->where('livro_id', $livro->id)->first();

        if ($item) {
            $item->increment('quantidade');
        } else {
            ItemCarrinho::create([
                'carrinho_id' => $carrinho->id,
                'livro_id' => $livro->id,
                'quantidade' => 1,
                'preco_unitario' => $livro->preco ?? 0,
            ]);
        }

        return redirect()->route('carrinho.index')->with('success', 'Livro adicionado ao carrinho!');
    }

    public function remover(ItemCarrinho $item)
    {
        $carrinho = $this->getCarrinho();

        if ($item->carrinho_id !== $carrinho->id) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('carrinho.index')->with('success', 'Item removido do carrinho.');
    }

    public function atualizar(Request $request, ItemCarrinho $item)
    {
        $carrinho = $this->getCarrinho();

        if ($item->carrinho_id !== $carrinho->id) {
            abort(403);
        }

        $request->validate([
            'quantidade' => 'required|integer|min:1|max:10',
        ]);

        $item->update(['quantidade' => $request->quantidade]);

        return redirect()->route('carrinho.index')->with('success', 'Quantidade atualizada.');
    }

    public function checkout()
    {
        $carrinho = $this->getCarrinho();

        if ($carrinho->itens->isEmpty()) {
            return redirect()->route('carrinho.index')->with('error', 'O carrinho está vazio.');
        }

        // Validar que todos os livros têm preço
        foreach ($carrinho->itens as $item) {
            if ($item->livro->preco === null || $item->livro->preco == 0) {
                return redirect()->route('carrinho.index')
                    ->with('error', "O livro {$item->livro->nome} não tem preço definido. Não pode ser comprado.");
            }
        }

    return view('carrinho.checkout', compact('carrinho'));
    }

    private function getCarrinho()
    {
        $user = Auth::user();

        if ($user) {
            $carrinho = Carrinho::with('itens.livro')
                ->where('user_id', $user->id)
                ->first();

            if (!$carrinho) {
                $carrinho = Carrinho::create(['user_id' => $user->id]);
            }
        } else {
            $sessionId = Session::getId();
            $carrinho = Carrinho::with('itens.livro')
                ->where('session_id', $sessionId)
                ->first();

            if (!$carrinho) {
                $carrinho = Carrinho::create(['session_id' => $sessionId]);
            }
        }

        return $carrinho;
    }
}
