<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    protected $pdfData;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $pdfData = null)
    {
        $this->booking = $booking->load('room');
        $this->pdfData = $pdfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmed - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->pdfData) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->pdfData,
                'Payment_Receipt_' . $this->booking->booking_reference . '.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
