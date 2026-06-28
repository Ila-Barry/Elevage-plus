<?php
// app/Mail/NewsletterMail.php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * NewsletterMail
 * 
 * Email de newsletter envoyé aux utilisateurs par l'admin
 */
class NewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $subjectText;
    protected string $content;
    protected User $user;

    /**
     * Créer une nouvelle instance
     */
    public function __construct(string $subject, string $content, User $user)
    {
        $this->subjectText = $subject;
        $this->content = $content;
        $this->user = $user;
    }

    /**
     * Enveloppe de l'email
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📧 ' . $this->subjectText,
        );
    }

    /**
     * Contenu de l'email
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'subject' => $this->subjectText,
                'content' => $this->content,
                'user' => $this->user,
            ],
        );
    }
}