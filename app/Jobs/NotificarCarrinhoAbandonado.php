<?php

namespace App\Jobs;

use App\Models\Carrinho;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\CarrinhoAbandonado;

class NotificarCarrinhoAbandonado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $carrinho;

    public function __construct(Carrinho $carrinho)
    {
        $this->carrinho = $carrinho;
    }

    public function handle()
    {
        // Verificar se o carrinho ainda existe e não foi convertido em encomenda
        if ($this->carrinho && $this->carrinho->itens()->count() > 0 && $this->carrinho->user) {
            Mail::to($this->carrinho->user->email)
                ->send(new CarrinhoAbandonado($this->carrinho));
        }
    }
}
