<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LivroController;
use App\Http\Controllers\Admin\AutorController;
use App\Http\Controllers\Admin\EditoraController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\LivroController as PublicLivroController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\StripeWebhookController;

// Página pública
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Rotas protegidas por autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // Carrinho de Compras
    Route::get('/carrinho', [App\Http\Controllers\CarrinhoController::class, 'index'])->name('carrinho.index');
    Route::post('/carrinho/adicionar/{livro}', [App\Http\Controllers\CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
    Route::delete('/carrinho/remover/{item}', [App\Http\Controllers\CarrinhoController::class, 'remover'])->name('carrinho.remover');
    Route::put('/carrinho/atualizar/{item}', [App\Http\Controllers\CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');
    Route::get('/carrinho/checkout', [App\Http\Controllers\CarrinhoController::class, 'checkout'])->name('carrinho.checkout');

    // Encomendas
    Route::get('/encomendas', [EncomendaController::class, 'index'])->name('encomendas.index');
    Route::get('/encomendas/{encomenda}', [EncomendaController::class, 'show'])->name('encomendas.show');
    Route::post('/encomenda/{encomenda}/pagamento', [EncomendaController::class, 'pagamento'])->name('encomendas.pagamento');
    Route::get('/encomenda/{encomenda}/confirmar-pagamento', [EncomendaController::class, 'confirmarPagamento'])->name('encomendas.pagamento.confirmar');
    Route::post('/encomenda/pagamento/criar', [EncomendaController::class, 'criarPagamento'])->name('encomenda.pagamento.criar');

    // Webhook do Stripe (sem auth, sem CSRF)
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

    // Pagamento
    Route::post('/encomendas/{encomenda}/pagamento', [App\Http\Controllers\EncomendaController::class, 'pagamento'])
        ->name('encomendas.pagamento');

    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

    // Rotas para 2FA
    Route::prefix('user')->name('profile.')->group(function () {
        Route::get('/two-factor', [TwoFactorController::class, 'index'])->name('two-factor');
        Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
        Route::post('/two-factor/confirm', [TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
        Route::delete('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');
    });

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rotas de visualização para clientes
    Route::middleware(['permission:view_books'])->group(function () {
        Route::view('/livros', 'livros.index')->name('livros.index');
    });

    Route::middleware(['permission:view_authors'])->group(function () {
        Route::view('/autores', 'autores.index')->name('autores.index');
    });

    Route::middleware(['permission:view_publishers'])->group(function () {
        Route::view('/editoras', 'editoras.index')->name('editoras.index');
    });

    // Rota para página individual do livro
    Route::middleware(['permission:view_books'])->group(function () {
        Route::get('/livro/{id}/{slug?}', [PublicLivroController::class, 'show'])->name('livros.show');
    });

    // Rotas de Requisições (sem o resource, com rotas manuais)
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::get('/requisicoes/create/{livro}', [RequisicaoController::class, 'create'])->name('requisicoes.create');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])->name('requisicoes.show');
    Route::delete('/requisicoes/{requisicao}', [RequisicaoController::class, 'destroy'])->name('requisicoes.destroy');
    // Rotas adicionais
    Route::put('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmarEntrega'])
        ->name('requisicoes.confirmar-entrega');
    Route::delete('/requisicoes/{requisicao}/cancelar', [RequisicaoController::class, 'cancelar'])
        ->name('requisicoes.cancelar');
    // Rota para criar requisição a partir de um livro (mantém esta)
    Route::get('/livros/{livro}/requisitar', [RequisicaoController::class, 'create'])
        ->name('livros.requisitar');

    // Rotas de Reviews (cidadãos)
    Route::middleware(['auth'])->group(function () {
        Route::get('/requisicoes/{requisicao}/review', [App\Http\Controllers\ReviewController::class, 'create'])
            ->name('reviews.create');
        Route::post('/requisicoes/{requisicao}/review', [App\Http\Controllers\ReviewController::class, 'store'])
            ->name('reviews.store');
    });
    // Rotas de moderação (admin)
    Route::middleware(['auth', 'permission:manage_users'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reviews', [App\Http\Controllers\ReviewController::class, 'moderarIndex'])
            ->name('reviews.index');
        Route::get('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'moderarShow'])
            ->name('reviews.show');
        Route::patch('/reviews/{review}/aprovar', [App\Http\Controllers\ReviewController::class, 'aprovar'])
            ->name('reviews.aprovar');
        Route::patch('/reviews/{review}/recusar', [App\Http\Controllers\ReviewController::class, 'recusar'])
            ->name('reviews.recusar');
    });

    // Rotas para notificações de disponibilidade
    Route::middleware(['auth'])->group(function () {
        Route::post('/livros/{livro}/notificar-disponibilidade', [App\Http\Controllers\NotificacaoDisponibilidadeController::class, 'store'])
            ->name('livros.notificar');
        Route::delete('/livros/{livro}/cancelar-notificacao', [App\Http\Controllers\NotificacaoDisponibilidadeController::class, 'destroy'])
            ->name('livros.cancelar-notificacao');
    });

    // PAINEL DE ADMINISTRAÇÃO
    Route::prefix('admin')->name('admin.')->group(function () {

        // Gestão de Livros
        Route::middleware(['permission:manage_books'])->group(function () {
            Route::get('/livros', [LivroController::class, 'index'])->name('livros');
            Route::get('/livros/novo', [LivroController::class, 'create'])->name('livros.novo');
            Route::post('/livros', [LivroController::class, 'store'])->name('livros.store');
            Route::get('/livros/{id}/editar', [LivroController::class, 'edit'])->name('livros.editar');
            Route::put('/livros/{id}', [LivroController::class, 'update'])->name('livros.update');
            Route::delete('/livros/{id}', [LivroController::class, 'destroy'])->name('livros.eliminar');
        });

        // Gestão de Autores
        Route::middleware(['permission:manage_authors'])->group(function () {
            Route::get('/autores', [AutorController::class, 'index'])->name('autores');
            Route::get('/autores/novo', [AutorController::class, 'create'])->name('autores.novo');
            Route::post('/autores', [AutorController::class, 'store'])->name('autores.store');
            Route::get('/autores/{id}/editar', [AutorController::class, 'edit'])->name('autores.editar');
            Route::put('/autores/{id}', [AutorController::class, 'update'])->name('autores.update');
            Route::delete('/autores/{id}', [AutorController::class, 'destroy'])->name('autores.destroy');
        });

        // Gestão de Editoras
        Route::middleware(['permission:manage_publishers'])->group(function () {
            Route::get('/editoras', [EditoraController::class, 'index'])->name('editoras');
            Route::get('/editoras/novo', [EditoraController::class, 'create'])->name('editoras.novo');
            Route::post('/editoras', [EditoraController::class, 'store'])->name('editoras.store');
            Route::get('/editoras/{id}/editar', [EditoraController::class, 'edit'])->name('editoras.editar');
            Route::put('/editoras/{id}', [EditoraController::class, 'update'])->name('editoras.update');
            Route::delete('/editoras/{id}', [EditoraController::class, 'destroy'])->name('editoras.destroy');
        });

        // Gestão de Utilizadores e Roles
        Route::middleware(['permission:manage_users'])->group(function () {
            // Utilizadores
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{id}/editar', [UserController::class, 'edit'])->name('users.editar');
            Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

            // Roles
            Route::get('/roles', [RoleController::class, 'index'])->name('roles');
            Route::get('/roles/novo', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{id}/editar', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        // Rotas para importação de livros
        Route::middleware(['permission:manage_books'])->prefix('importar')->name('importar.')->group(function () {
            Route::get('/livros', [App\Http\Controllers\Admin\ImportacaoController::class, 'index'])->name('index');
            Route::post('/livros/pesquisar', [App\Http\Controllers\Admin\ImportacaoController::class, 'pesquisar'])->name('pesquisar');
            Route::post('/livros/importar', [App\Http\Controllers\Admin\ImportacaoController::class, 'importar'])->name('importar');
            Route::post('/livros/importar-multiplos', [App\Http\Controllers\Admin\ImportacaoController::class, 'importarMultiplos'])->name('importar-multiplos');
        });
    });
});
