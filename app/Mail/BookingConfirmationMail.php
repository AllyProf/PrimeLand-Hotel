<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $password;
    public $paymentPercentage;
    public $remainingAmount;
    public $generalNotes;
    protected $pdfData;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $password, $paymentPercentage = null, $remainingAmount = null, $generalNotes = null, $pdfData = null)
    {
        $this->booking = $booking->load('room');
        $this->password = $password;
        $this->paymentPercentage = $paymentPercentage ?? $booking->payment_percentage;
        $this->remainingAmount = $remainingAmount ?? ($booking->total_price - ($booking->amount_paid ?? 0));
        $this->generalNotes = $generalNotes;
        $this->pdfData = $pdfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->booking->status === 'pending' || str_contains(strtolower($this->generalNotes ?? ''), 'proforma')) 
            ? 'Proforma Invoice - ' . $this->booking->booking_reference . ' - PrimeLand Hotel'
            : 'Booking Confirmation - ' . $this->booking->booking_reference . ' - PrimeLand Hotel';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->pdfData) {
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->pdfData,
                'PrimeLand_Booking_Invoice.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
