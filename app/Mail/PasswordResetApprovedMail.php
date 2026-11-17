<?php

namespace App\Mail;

use App\Models\Resident;
use App\Models\PasswordResetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resident;
    public $resetRequest;

    public function __construct(Resident $resident, PasswordResetRequest $resetRequest)
    {
        $this->resident = $resident;
        $this->resetRequest = $resetRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Password Reset Request Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-approved',
        );
    }
}