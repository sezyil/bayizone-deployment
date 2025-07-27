<?php

namespace App\Mail\CompanyCustomer;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Models\Customer\CustomerOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;
    protected $order;
    protected $new_status;
    protected $note;

    /**
     * Create a new message instance.
     */
    public function __construct(CustomerOrder $order, string $new_status, string $note = null)
    {
        $this->order = $order;
        $this->new_status = $new_status;
        $this->note = $note;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email/company-customer/order-status-changed.subject').' - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.company_customer.status_change_company_customer',
            with: [
                'order' => $this->order,
                'new_status' => CustomerOrderStatusEnum::description($this->new_status),
                'title' => __('email/company-customer/order-status-changed.subject'),
                'note' => $this->note,
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
