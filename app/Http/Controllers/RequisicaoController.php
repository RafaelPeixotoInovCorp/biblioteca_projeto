<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\NotificacaoDisponibilidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\LivroDisponivelNotificacao;
use Illuminate\Support\Facades\Mail;

class RequisicaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        Log::info('RequisicaoController@index', ['user_id' => $user->id]);

        if ($user->isAdmin()) {
            $requisicoes = Requisicao::with(['livro', 'cidadao'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $indicadores = [
                'ativas' => Requisicao::whereIn('status', ['pendente', 'aprovado'])->count(),
                'ultimos_30_dias' => Requisicao::where('created_at', '>=', now()->subDays(30))->count(),
                'entregues_hoje' => Requisicao::whereDate('data_efetiva_entrega', now())->count(),
            ];
        } else {
            $requisicoes = Requisicao::with('livro')
                ->where('cidadao_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $indicadores = [
                'ativas' => Requisicao::where('cidadao_id', $user->id)
                    ->whereIn('status', ['pendente', 'aprovado'])->count(),
                'ultimos_30_dias' => Requisicao::where('cidadao_id', $user->id)
                    ->where('created_at', '>=', now()->subDays(30))->count(),
                'entregues_hoje' => Requisicao::where('cidadao_id', $user->id)
                    ->whereDate('data_efetiva_entrega', now())->count(),
            ];
        }

        return view('requisicoes.index', compact('requisicoes', 'indicadores'));
    }

    public function create($livroId)
    {
        Log::info('RequisicaoController@create', ['livro_id' => $livroId]);

        try {
            $livro = Livro::findOrFail($livroId);
        } catch (\Exception $e) {
            Log::error('Livro não encontrado', ['livro_id' => $livroId]);
            return redirect()->route('livros.index')->with('error', 'Livro não encontrado.');
        }

        $user = Auth::user();

        // Verificar se livro está disponível
        if (!$livro->isDisponivel()) {
            return redirect()->back()->with('error', 'Este livro não está disponível para requisição.');
        }

        // Verificar se cidadão pode requisitar (máx 3)
        if (!$user->isAdmin() && !$user->podeRequisitar()) {
            return redirect()->back()->with('error', 'Já tem 3 livros requisitados. Devolva alguns antes de requisitar mais.');
        }

        return view('requisicoes.create', compact('livro'));
    }

    public function store(Request $request)
    {
        Log::info('RequisicaoController@store INÍCIO', [
            'request_data' => $request->all()
        ]);

        $user = Auth::user();

        // Validar o request
        $request->validate([
            'livro_id' => 'required|exists:livros,id',
            'observacoes' => 'nullable|string',
        ]);

        try {
            $livro = Livro::findOrFail($request->livro_id);
        } catch (\Exception $e) {
            Log::error('Livro não encontrado', ['livro_id' => $request->livro_id]);
            return redirect()->back()->with('error', 'Livro não encontrado.');
        }

        // Verificar se livro está disponível
        if (!$livro->isDisponivel()) {
            return redirect()->back()->with('error', 'Este livro não está disponível para requisição.');
        }

        // Verificar se cidadão pode requisitar
        if (!$user->isAdmin() && !$user->podeRequisitar()) {
            return redirect()->back()->with('error', 'Limite de 3 requisições atingido.');
        }

        // Gerar número sequencial
        $ultimoNumero = Requisicao::max('id') ?? 0;
        $numeroRequisicao = 'REQ-' . str_pad($ultimoNumero + 1, 6, '0', STR_PAD_LEFT);

        Log::info('A criar requisição', [
            'numero' => $numeroRequisicao,
            'livro_id' => $livro->id,
            'user_id' => $user->id
        ]);

        try {
            $requisicao = Requisicao::create([
                'numero_requisicao' => $numeroRequisicao,
                'livro_id' => $livro->id,
                'cidadao_id' => $user->id,
                'data_requisicao' => now(),
                'data_prevista_entrega' => now()->addDays(5),
                'status' => 'pendente',
                'observacoes' => $request->observacoes,
            ]);

            Log::info('Requisição criada', ['requisicao_id' => $requisicao->id]);

            return redirect()->route('requisicoes.index')
                ->with('success', 'Requisição criada com sucesso!');

        } catch (\Exception $e) {
            Log::error('Erro ao criar requisição', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao criar requisição: ' . $e->getMessage());
        }
    }

    public function show(Requisicao $requisicao)
    {
        $user = Auth::user();
        Log::info('RequisicaoController@show', ['requisicao_id' => $requisicao->id, 'user_id' => $user->id]);

        if (!$user->isAdmin() && $requisicao->cidadao_id !== $user->id) {
            abort(403);
        }

        return view('requisicoes.show', compact('requisicao'));
    }

    // Confirma a entrega de um livro e processa notificações pendentes

    public function confirmarEntrega(Request $request, Requisicao $requisicao)
    {
        $user = Auth::user();
        Log::info('RequisicaoController@confirmarEntrega', ['requisicao_id' => $requisicao->id, 'user_id' => $user->id]);

        if (!$user->isAdmin()) {
            abort(403);
        }

        if (!in_array($requisicao->status, ['pendente', 'aprovado'])) {
            return redirect()->back()->with('error', 'Esta requisição não pode ser entregue.');
        }

        $dataEntrega = now();
        $diasAtraso = max(0, $dataEntrega->diffInDays($requisicao->data_prevista_entrega, false));

        $requisicao->update([
            'data_efetiva_entrega' => $dataEntrega,
            'dias_atraso' => $diasAtraso,
            'status' => 'entregue',
            'admin_id' => $user->id,
        ]);

        // Reativar livro
        $livro = $requisicao->livro;
        $livro->update(['disponivel' => true]);

        // Processar notificações para este livro
        $notificacoesEnviadas = $this->processarNotificacoesDisponibilidade($livro);

        if ($notificacoesEnviadas > 0) {
            session()->flash('info', "📧 {$notificacoesEnviadas} cidadão(s) notificado(s) sobre a disponibilidade do livro.");
        }

        return redirect()->route('requisicoes.show', $requisicao)
            ->with('success', 'Entrega confirmada!');
    }

    /**
     * Processa as notificações de disponibilidade para um livro
     */
    private function processarNotificacoesDisponibilidade(Livro $livro): int
    {
        $notificacoes = NotificacaoDisponibilidade::where('livro_id', $livro->id)
            ->where('notificado', false)
            ->with('cidadao')
            ->get();

        $enviadas = 0;

        foreach ($notificacoes as $notificacao) {
            try {
                Mail::to($notificacao->cidadao->email)
                    ->send(new LivroDisponivelNotificacao($livro, $notificacao->cidadao));

                $notificacao->update([
                    'notificado' => true,
                    'notificado_em' => now(),
                ]);

                $enviadas++;
                Log::info('Notificação de disponibilidade enviada', [
                    'livro_id' => $livro->id,
                    'cidadao_id' => $notificacao->cidadao_id,
                    'email' => $notificacao->cidadao->email
                ]);

            } catch (\Exception $e) {
                Log::error('Erro ao enviar notificação de disponibilidade', [
                    'notificacao_id' => $notificacao->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $enviadas;
    }

    public function cancelar(Requisicao $requisicao)
    {
        $user = Auth::user();
        Log::info('RequisicaoController@cancelar', ['requisicao_id' => $requisicao->id, 'user_id' => $user->id]);

        if (!$user->isAdmin() && $requisicao->cidadao_id !== $user->id) {
            abort(403);
        }

        if ($requisicao->status !== 'pendente') {
            return redirect()->back()->with('error', 'Apenas requisições pendentes podem ser canceladas.');
        }

        $requisicao->update(['status' => 'cancelado']);
        $requisicao->livro->update(['disponivel' => true]);

        return redirect()->route('requisicoes.index')
            ->with('success', 'Requisição cancelada.');
    }

    public function destroy(Requisicao $requisicao)
    {
        // Método destroy para o resource (pode apagar se não for usado)
        abort(404);
    }
}
