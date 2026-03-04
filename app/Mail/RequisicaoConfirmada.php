<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisicaoConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public Requisicao $requisicao;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Requisição Confirmada - ' . $this->requisicao->numero_requisicao,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requisicao-confirmada',
        );
    }
}
