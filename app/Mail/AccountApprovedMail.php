<?php

namespace App\Mail;

use App\Models\Resident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resident;

    public function __construct(Resident $resident)
    {
        $this->resident = $resident;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Barangay BIMS Account Has Been Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-approved',
        );
    }
}