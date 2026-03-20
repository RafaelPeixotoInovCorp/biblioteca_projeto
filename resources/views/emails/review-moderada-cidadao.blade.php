<!DOCTYPE html>
<html>
<head>
    <title>Review {{ $review->estado === 'ativo' ? 'Aprovada' : 'Recusada' }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: {{ $review->estado === 'ativo' ? '#10b981' : '#ef4444' }}; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .review-box { background: white; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Review {{ $review->estado === 'ativo' ? 'Aprovada' : 'Recusada' }}</h1>
        </div>

        <div class="content">
            <p>Olá, {{ $review->cidadao->name }}!</p>

            <p>A sua review para o livro <strong>"{{ $review->livro->nome }}"</strong> foi
                <strong>{{ $review->estado === 'ativo' ? 'aprovada' : 'recusada' }}</strong>.
            </p>

            <div class="review-box">
                <p><strong>Nota:</strong> {{ $review->nota }} ★</p>
                @if($review->comentario)
                    <p><strong>Comentário:</strong><br>{{ $review->comentario }}</p>
                @endif
            </div>

            @if($review->estado === 'recusado' && $review->justificacao_recusa)
                <div style="background: #fee2e2; padding: 15px; border-radius: 5px; margin-top: 20px;">
                    <p><strong>Justificação da recusa:</strong></p>
                    <p>{{ $review->justificacao_recusa }}</p>
                </div>
            @endif

            @if($review->estado === 'ativo')
                <p>A sua review está agora visível para todos os utilizadores na página do livro.</p>
            @endif
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Biblioteca. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
