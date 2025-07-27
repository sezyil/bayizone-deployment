<?php

namespace App\Mail\System;

use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;

class CustomerForgotPassword extends Mailable
{
    use Queueable, SerializesModels;
    public User $user;
    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Şifre Sıfırlama - Bayizone',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        //delete old tokens
        $externalUri = env('CLIENT_APP_URL');
        Password::broker()->getRepository()->delete($this->user);
        //create new token
        $token = Password::createToken($this->user);
        $expireMin= config('auth.passwords.users.expire');
        return new Content(
            view: 'emails.system.auth.forgotpassword',
            with: [
                'user' => $this->user,
                'title' => 'Şifre Sıfırlama',
                'uri' => $externalUri . '/auth/reset-password?u=' . $this->user->id . '&t=' . $token,
                'expireMinutes' => $expireMin
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
