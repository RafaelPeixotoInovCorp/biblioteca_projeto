<!DOCTYPE html>
<html>
<head>
    <title>Livro Disponível</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .book-info { display: flex; gap: 20px; margin: 20px 0; background: white; padding: 20px; border-radius: 5px; }
        .button { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Livro Disponível!</h1>
    </div>

    <div class="content">
        <p>Olá, <strong>{{ $cidadao->name }}</strong>!</p>

        <p>O livro que estava à espera já está disponível para requisição.</p>

        <div class="book-info">
            @if($livro->imagem_capa)
                <img src="{{ Storage::url($livro->imagem_capa) }}"
                     alt="{{ $livro->nome }}"
                     style="width: 100px; height: 140px; object-fit: cover; border-radius: 5px;">
            @else
                <div style="width: 100px; height: 140px; background: #ddd; display: flex; align-items: center; justify-content: center;">
                </div>
            @endif

            <div>
                <h3>{{ $livro->nome }}</h3>
                <p><strong>ISBN:</strong> {{ $livro->isbn }}</p>
                <p><strong>Editora:</strong> {{ $livro->editora?->nome ?? 'N/A' }}</p>
                @if($livro->autores->isNotEmpty())
                    <p><strong>Autor(es):</strong> {{ $livro->autores->pluck('nome')->implode(', ') }}</p>
                @endif
            </div>
        </div>

        <p style="text-align: center; margin-top: 30px;">
            <a href="{{ route('livros.show', ['id' => $livro->id, 'slug' => \Str::slug($livro->nome)]) }}" class="button">
                Ver Livro e Requisitar
            </a>
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Biblioteca. Todos os direitos reservados.</p>
    </div>
</div>
</body>
</html>
