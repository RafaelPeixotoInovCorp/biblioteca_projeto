<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisicaoConfirmada;
use App\Mail\RequisicaoAdminAlerta;
use App\Mail\RequisicaoReminder;

class RequisicaoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin vê todas as requisições
            $requisicoes = Requisicao::with(['livro', 'cidadao'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $indicadores = [
                'ativas' => Requisicao::whereIn('status', ['pendente', 'aprovado'])->count(),
                'ultimos_30_dias' => Requisicao::where('created_at', '>=', now()->subDays(30))->count(),
                'entregues_hoje' => Requisicao::whereDate('data_efetiva_entrega', now())->count(),
            ];
        } else {
            // Cidadão vê apenas as suas requisições
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

    public function create(Livro $livro)
    {
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

    public function store(Request $request, Livro $livro)
    {
        $user = Auth::user();

        // Validações
        if (!$livro->isDisponivel()) {
            return redirect()->back()->with('error', 'Este livro já não está disponível.');
        }

        if (!$user->isAdmin() && !$user->podeRequisitar()) {
            return redirect()->back()->with('error', 'Limite de 3 requisições atingido.');
        }

        // Gerar número sequencial
        $ultimoNumero = Requisicao::max('id') ?? 0;
        $numeroRequisicao = 'REQ-' . str_pad($ultimoNumero + 1, 6, '0', STR_PAD_LEFT);

        // Criar requisição
        $requisicao = Requisicao::create([
            'numero_requisicao' => $numeroRequisicao,
            'livro_id' => $livro->id,
            'cidadao_id' => $user->id,
            'data_requisicao' => now(),
            'data_prevista_entrega' => now()->addDays(5),
            'status' => $user->isAdmin() ? 'aprovado' : 'pendente',
            'observacoes' => $request->observacoes,
        ]);

        // Atualizar disponibilidade do livro
        $livro->disponivel = false;
        $livro->requisicoes_count++;
        $livro->save();

        // Enviar emails
        Mail::to($user->email)->send(new RequisicaoConfirmada($requisicao));

        // Enviar email para todos os admins
        $admins = User::whereHas('roles', function($q) {
            $q->where('name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new RequisicaoAdminAlerta($requisicao));
        }

        return redirect()->route('requisicoes.show', $requisicao)
            ->with('success', 'Requisição criada com sucesso!');
    }

    public function show(Requisicao $requisicao)
    {
        $user = Auth::user();

        // Verificar permissão
        if (!$user->isAdmin() && $requisicao->cidadao_id !== $user->id) {
            abort(403);
        }

        return view('requisicoes.show', compact('requisicao'));
    }

    public function confirmarEntrega(Request $request, Requisicao $requisicao)
    {
        $user = Auth::user();

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
        $requisicao->livro->update(['disponivel' => true]);

        return redirect()->route('requisicoes.show', $requisicao)
            ->with('success', 'Entrega confirmada!');
    }

    public function cancelar(Requisicao $requisicao)
    {
        $user = Auth::user();

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

    // Comando para enviar reminders (para ser executado diariamente via cron)
    public function enviarReminders()
    {
        $requisicoes = Requisicao::with(['cidadao', 'livro'])
            ->where('status', 'aprovado')
            ->whereDate('data_prevista_entrega', now()->addDay())
            ->get();

        foreach ($requisicoes as $requisicao) {
            Mail::to($requisicao->cidadao->email)
                ->send(new RequisicaoReminder($requisicao));
        }

        return response()->json(['message' => 'Reminders enviados com sucesso']);
    }
}
