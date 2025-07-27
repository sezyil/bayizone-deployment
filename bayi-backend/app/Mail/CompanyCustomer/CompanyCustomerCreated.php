<?php

namespace App\Mail\CompanyCustomer;

use App\Models\CompanyCustomer\CompanyCustomerUser;
use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class CompanyCustomerCreated extends Mailable
{
    use Queueable, SerializesModels;
    public CompanyCustomerUser $user;
    public string $password;
    /**
     * Create a new message instance.
     */
    public function __construct(CompanyCustomerUser $user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email/company-customer/created-new-company-customer.title').' - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        //delete old tokens
        $appUrl = env('DEALER_APP_URL');
        //create new token
        return new Content(
            view: 'emails.company_customer.created_new_company_customer',
            with: [
                'user' => $this->user,
                'title' => __('email/company-customer/created-new-company-customer.title'),
                'password' => $this->password,
                'company_name' => $this->user->customer->firm_name,
                'uri' => $appUrl ?? '---',
                'code' => $this->user->companyCustomer->code ?? '---',
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
