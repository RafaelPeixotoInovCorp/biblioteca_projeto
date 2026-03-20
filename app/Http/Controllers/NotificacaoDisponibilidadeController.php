<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\NotificacaoDisponibilidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificacaoDisponibilidadeController extends Controller
{
    /**
     * Cria uma notificação para um livro indisponível
     */
    public function store(Request $request, Livro $livro)
    {
        $user = Auth::user();

        Log::info('NotificacaoDisponibilidade@store', [
            'livro_id' => $livro->id,
            'user_id' => $user->id
        ]);

        // Verificar se o livro está mesmo indisponível
        if ($livro->isDisponivel()) {
            return response()->json([
                'success' => false,
                'message' => 'Este livro já está disponível. Pode requisitá-lo agora!'
            ], 400);
        }

        // Verificar se já tem notificação ativa
        $notificacaoExistente = NotificacaoDisponibilidade::where('livro_id', $livro->id)
            ->where('cidadao_id', $user->id)
            ->where('notificado', false)
            ->first();

        if ($notificacaoExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Já está inscrito para receber notificação deste livro.'
            ], 400);
        }

        // Criar notificação
        $notificacao = NotificacaoDisponibilidade::create([
            'livro_id' => $livro->id,
            'cidadao_id' => $user->id,
            'notificado' => false,
        ]);

        Log::info('Nova notificação criada', [
            'livro_id' => $livro->id,
            'cidadao_id' => $user->id,
            'notificacao_id' => $notificacao->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receberá um email quando o livro estiver disponível!'
        ]);
    }

    /**
     * Cancela uma notificação
     */
    public function destroy(Request $request, Livro $livro)
    {
        $user = Auth::user();

        Log::info('NotificacaoDisponibilidade@destroy', [
            'livro_id' => $livro->id,
            'user_id' => $user->id
        ]);

        $notificacao = NotificacaoDisponibilidade::where('livro_id', $livro->id)
            ->where('cidadao_id', $user->id)
            ->where('notificado', false)
            ->first();

        if ($notificacao) {
            $notificacao->delete();

            Log::info('Notificação cancelada', [
                'livro_id' => $livro->id,
                'cidadao_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notificação cancelada.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada.'
        ], 404);
    }
}
