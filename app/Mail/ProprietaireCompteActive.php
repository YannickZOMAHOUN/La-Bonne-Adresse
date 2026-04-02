<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProprietaireCompteActive extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Votre compte est activé — Bonnes Adresses Bénin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.proprietaire-compte-active',
            with: [
                'user'     => $this->user,
                'loginUrl' => route('login'),
            ],
        );
    }
}
