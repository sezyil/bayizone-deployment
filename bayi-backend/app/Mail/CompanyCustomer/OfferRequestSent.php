<?php

namespace App\Mail\CompanyCustomer;

use App\Models\OfferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferRequestSent extends Mailable
{
    use Queueable, SerializesModels;

    protected $offerRequest;
    protected $lang;

    /**
     * Create a new message instance.
     */
    public function __construct(OfferRequest $offerRequest, string $lang)
    {
        $this->offerRequest = $offerRequest;
        $this->lang = $lang;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email/company-customer/offer-request-sent.title') . ' - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.company_customer.customer_offer_request_sent',
            with: [
                'data' => $this->offerRequest,
                'title' => __('email/company-customer/offer-request-sent.title'),
                'lang' => $this->lang
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
