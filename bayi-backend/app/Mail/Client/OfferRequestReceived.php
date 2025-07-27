<?php

namespace App\Mail\Client;

use App\Models\OfferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    protected $offerRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(OfferRequest $offerRequest)
    {
        $this->offerRequest = $offerRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Teklif Talebi Aldınız - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client.client_offer_request_received',
            with: [
                'data' => $this->offerRequest,
                'title' => 'Teklif Talebi Aldınız',
            ],
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
