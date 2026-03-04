<!DOCTYPE html>
<html>
<head>
    <title>Requisição Confirmada</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .book-info { display: flex; gap: 20px; margin: 20px 0; }
        .book-details { flex: 1; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 5px; font-size: 12px; }
        .badge-warning { background: #fbbf24; color: #000; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Requisição Confirmada</h1>
        <p>{{ $requisicao->numero_requisicao }}</p>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ $requisicao->cidadao->name }}</strong>!</p>

        <p>A sua requisição foi confirmada com sucesso.</p>

        <div class="book-info">
            @if($requisicao->livro->imagem_capa)
                <img src="{{ Storage::url($requisicao->livro->imagem_capa) }}"
                     alt="{{ $requisicao->livro->nome }}"
                     style="width: 100px; height: 140px; object-fit: cover; border-radius: 5px;">
            @else
                <div style="width: 100px; height: 140px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                    📚
                </div>
            @endif

            <div class="book-details">
                <h3>{{ $requisicao->livro->nome }}</h3>
                <p><strong>ISBN:</strong> {{ $requisicao->livro->isbn }}</p>
                <p><strong>Editora:</strong> {{ $requisicao->livro->editora?->nome ?? 'N/A' }}</p>
            </div>
        </div>

        <div style="background: white; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Data da Requisição:</strong> {{ $requisicao->data_requisicao->format('d/m/Y') }}</p>
            <p><strong>Data Prevista para Entrega:</strong> {{ $requisicao->data_prevista_entrega->format('d/m/Y') }}</p>
            <p><strong>Status:</strong>
                <span class="badge badge-warning">{{ $requisicao->status }}</span>
            </p>
        </div>

        <p>Pode acompanhar o estado da sua requisição na plataforma.</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Biblioteca. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
