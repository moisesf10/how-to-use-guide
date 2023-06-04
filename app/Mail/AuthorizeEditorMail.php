<?php

namespace App\Mail;

use App\Models\Token;
use App\Models\Workspace;
use App\Models\WorkspaceEditor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AuthorizeEditorMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $mailName = 'AuthorizeEditorMail';

    public Workspace $workspace;
    public WorkspaceEditor $editor;
    public Token $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Workspace $workspace, WorkspaceEditor $editor, Token $token)
    {
        $this->workspace = $workspace;
        $this->editor = $editor;
        $this->token = $token;


    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitação Para Gerenciar Workspace',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.admin.authorizations.invite-editor-workspace',
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
