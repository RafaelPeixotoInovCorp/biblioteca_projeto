<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewModeradaCidadao extends Mailable
{
    use Queueable, SerializesModels;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function envelope(): Envelope
    {
        $estado = $this->review->estado === 'ativo' ? 'Aprovada' : 'Recusada';
        return new Envelope(
            subject: "Review {$estado} - {$this->review->livro->nome}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-moderada-cidadao',
        );
    }
}
