<!DOCTYPE html>
<html>
<head>
    <title>Nova Review Submetida</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .review-box { background: white; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { display: inline-block; padding: 10px 20px; background: #4f46e5; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Nova Review Submetida</h1>
    </div>

    <div class="content">
        <p>Olá, Administrador!</p>

        <p>Foi submetida uma nova review que aguarda moderação.</p>

        <div class="review-box">
            <p><strong>Cidadão:</strong> {{ $review->cidadao->name }}</p>
            <p><strong>Email:</strong> {{ $review->cidadao->email }}</p>
            <p><strong>Livro:</strong> {{ $review->livro->nome }}</p>
            <p><strong>Nota:</strong> {{ $review->nota }} ★</p>
            @if($review->comentario)
                <p><strong>Comentário:</strong><br>{{ $review->comentario }}</p>
            @endif
        </div>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ route('admin.reviews.moderar', $review) }}" class="button">Moderar Review</a>
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Biblioteca. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
