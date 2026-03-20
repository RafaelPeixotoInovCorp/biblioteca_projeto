<?php

namespace App\Mail;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelNotificacao extends Mailable
{
    use Queueable, SerializesModels;

    public Livro $livro;
    public User $cidadao;

    public function __construct(Livro $livro, User $cidadao)
    {
        $this->livro = $livro;
        $this->cidadao = $cidadao;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📚 Livro Disponível: ' . $this->livro->nome,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.livro-disponivel',
        );
    }
}
