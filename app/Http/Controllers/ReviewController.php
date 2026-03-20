<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewCriadaAdmin;
use App\Mail\ReviewModeradaCidadao;

class ReviewController extends Controller
{
    /**
     * Mostra formulário para criar review
     */
    public function create(Requisicao $requisicao)
    {
        $user = Auth::user();

        // Verificar se a requisição pertence ao cidadão
        if ($requisicao->cidadao_id !== $user->id) {
            abort(403);
        }

        // Verificar se a requisição já foi entregue
        if ($requisicao->status !== 'entregue') {
            return redirect()->back()->with('error', 'Apenas pode avaliar livros que já foram entregues.');
        }

        // Verificar se já existe review
        if ($requisicao->hasReview()) {
            return redirect()->back()->with('error', 'Já submeteu uma review para esta requisição.');
        }

        return view('reviews.create', compact('requisicao'));
    }

    /**
     * Guarda a review
     */
    public function store(Request $request, Requisicao $requisicao)
    {
        $user = Auth::user();

        $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        // Verificações de segurança
        if ($requisicao->cidadao_id !== $user->id) {
            abort(403);
        }

        if ($requisicao->status !== 'entregue') {
            return redirect()->back()->with('error', 'Apenas pode avaliar livros que já foram entregues.');
        }

        if ($requisicao->hasReview()) {
            return redirect()->back()->with('error', 'Já submeteu uma review para esta requisição.');
        }

        // Criar review
        $review = Review::create([
            'requisicao_id' => $requisicao->id,
            'livro_id' => $requisicao->livro_id,
            'cidadao_id' => $user->id,
            'nota' => $request->nota,
            'comentario' => $request->comentario,
            'estado' => 'suspenso',
        ]);

        // Enviar email para todos os admins
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            try {
                Mail::to($admin->email)->send(new ReviewCriadaAdmin($review));
            } catch (\Exception $e) {
                \Log::error('Erro ao enviar email para admin: ' . $e->getMessage());
            }
        }

        return redirect()->route('requisicoes.show', $requisicao)
            ->with('success', 'Review submetida com sucesso! Aguarde moderação.');
    }

    /**
     * Lista reviews para moderação (admin)
     */
    public function moderarIndex()
    {
        // Verificar se é admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $reviews = Review::with(['livro', 'cidadao'])
            ->where('estado', 'suspenso')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $estatisticas = [
            'suspensas' => Review::where('estado', 'suspenso')->count(),
            'ativas' => Review::where('estado', 'ativo')->count(),
            'recusadas' => Review::where('estado', 'recusado')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'estatisticas'));
    }

    /**
     * Mostra detalhe da review para moderação
     */
    public function moderarShow(Review $review)
    {
        // Verificar se é admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Aprova a review
     */
    public function aprovar(Request $request, Review $review)
    {
        // Verificar se é admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $review->update([
            'estado' => 'ativo',
            'moderado_por' => Auth::id(),
            'moderado_em' => now(),
        ]);

        // Notificar cidadão
        try {
            Mail::to($review->cidadao->email)->send(new ReviewModeradaCidadao($review));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email para cidadão: ' . $e->getMessage());
        }

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review aprovada com sucesso!');
    }

    /**
     * Recusa a review com justificação
     */
    public function recusar(Request $request, Review $review)
    {
        // Verificar se é admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'justificacao' => 'required|string|max:500',
        ]);

        $review->update([
            'estado' => 'recusado',
            'justificacao_recusa' => $request->justificacao,
            'moderado_por' => Auth::id(),
            'moderado_em' => now(),
        ]);

        // Notificar cidadão
        try {
            Mail::to($review->cidadao->email)->send(new ReviewModeradaCidadao($review));
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email para cidadão: ' . $e->getMessage());
        }

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review recusada com sucesso!');
    }

    /**
     * Lista reviews ativas de um livro (pública)
     */
    public function livroReviews($livroId)
    {
        $reviews = Review::with('cidadao')
            ->where('livro_id', $livroId)
            ->where('estado', 'ativo')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }
}
