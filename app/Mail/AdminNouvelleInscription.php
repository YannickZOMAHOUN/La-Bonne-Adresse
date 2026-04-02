<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNouvelleInscription extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔔 Nouvelle inscription à valider — Bonnes Adresses Bénin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-nouvelle-inscription',
            with: [
                'user'     => $this->user,
                'adminUrl' => route('admin.proprietaires'),
            ],
        );
    }
}
