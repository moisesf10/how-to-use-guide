<?php

namespace App\Mail;

use App\Models\Workspace;
use App\Models\WorkspaceUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteUserGuideMail extends Mailable
{
    use Queueable, SerializesModels;

    public WorkspaceUser  $authorizedUser;
    public Workspace $workspace;

    /**
     * Create a new message instance.
     */
    public function __construct(WorkspaceUser $authorizedUser, Workspace $workspace)
    {
        $this->authorizedUser = $authorizedUser;
        $this->workspace = $workspace;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Convite de acesso ao guia de uso',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.admin.authorizations.invite-user-guide-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
