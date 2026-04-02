<?php

namespace App\Mail;

use App\Models\Etablissement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EtablissementValide extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Etablissement $etablissement) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Votre fiche « {$this->etablissement->nom} » est en ligne !",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.etablissement-valide',
            with: [
                'etablissement' => $this->etablissement,
                'ficheUrl'      => route('adresses.show', $this->etablissement->slug),
            ],
        );
    }
}
