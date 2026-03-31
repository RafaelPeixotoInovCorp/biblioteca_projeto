use App\Jobs\NotificarCarrinhoAbandonado;
use App\Models\Carrinho;

protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        $carrinhosAbandonados = Carrinho::with('itens')
            ->where('updated_at', '<', now()->subHour())
            ->whereHas('itens')
            ->whereNotNull('user_id')
            ->get();

        foreach ($carrinhosAbandonados as $carrinho) {
            NotificarCarrinhoAbandonado::dispatch($carrinho);
        }
    })->hourly();
}
