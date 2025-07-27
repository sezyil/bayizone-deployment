<?php

namespace App\Mail\CompanyCustomer;

use App\Enums\CustomerOfferStatusEnum;
use App\Enums\CustomerOrderLineStatusEnum;
use App\Enums\CustomerOrderStatusEnum;
use App\Models\Customer\CustomerOrder;
use App\Models\Customer\CustomerOrderLine;
use App\Models\CustomerShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyCustomerShipment extends Mailable
{
    use Queueable, SerializesModels;
    protected $shipmentData;
    protected $isShipped;
    protected $isDelivered;
    protected $note;
    /**
     * Create a new message instance.
     */
    public function __construct(CustomerShipment $shipmentData, $note, bool $isShipped, bool $isDelivered)
    {
        $this->note = $note;
        $this->shipmentData = $shipmentData;
        $this->isShipped = $isShipped;
        $this->isDelivered = $isDelivered;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->isShipped
            ? __('email/company-customer/customer-order-shipment.subject.is_shipped')
            : __('email/company-customer/customer-order-shipment.subject.is_delivered')) . ' - Bayizone';
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $title = $this->isShipped
            ? __('email/company-customer/customer-order-shipment.subject.is_shipped')
            : __('email/company-customer/customer-order-shipment.subject.is_delivered');


        $bodyTextData = [
            'number' => $this->shipmentData->shipment_no,
        ];

        $bodyText = $this->isShipped
            ? __('email/company-customer/customer-order-shipment.body.is_shipped', $bodyTextData)
            : __('email/company-customer/customer-order-shipment.body.is_delivered', $bodyTextData);


        return new Content(
            view: 'emails.company_customer.customer_order_shipment',
            with: [
                'data' => $this->shipmentData,
                'bodyText' => $bodyText,
                'title' => $title,
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
