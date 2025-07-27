<?php

namespace App\Mail\CompanyCustomer;

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
    public function __construct(CustomerOffer $offer, string $new_status, bool $is_new = false)
    {
        $this->offer = $offer;
        $this->new_status = $new_status;
        $this->is_new = $is_new;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $title = $this->is_new
            ? __('email/company-customer/offer-status-changed.subject_new')
            : __('email/company-customer/offer-status-changed.subject_updated');
        return new Envelope(
            subject: $title . ' - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $title = $this->is_new
            ? __('email/company-customer/offer-status-changed.subject_new')
            : __('email/company-customer/offer-status-changed.subject_updated');
        return new Content(
            view: 'emails.company_customer.status_change_offer_company_customer',
            with: [
                'offer' => $this->offer,
                'status' => __('offer-status.' . $this->new_status),
                'title' => $title,
                'is_new' => $this->is_new,
                'redirect_url' => $this->offer->generatePreviewUri() . '&lang=' . app()->getLocale(),
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
