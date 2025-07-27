<?php

namespace App\Mail\Client;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferStatusChanged extends Mailable
{
    use Queueable, SerializesModels;
    protected $offer;
    protected $new_status;
    protected $is_new;

    /**
     * Create a new message instance.
     */
    public function __construct(CustomerOffer $offer, string $new_status)
    {
        $this->offer = $offer;
        $this->new_status = $new_status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            subject: 'Müşteri Teklif Durumu Değişti'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client.status_change_offer_client',
            with: [
                'offer' => $this->offer,
                'status' => CustomerOfferStatusEnum::description($this->new_status),
                'title' => 'Teklif Durumu Değişti',
            ]
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
