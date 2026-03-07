<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SimpleInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $subjectStr;
    public $invoiceType;
    public $notes;
    protected $pdfData;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $subjectStr, $invoiceType, $notes = null, $pdfData = null)
    {
        $this->name = $name;
        $this->subjectStr = $subjectStr;
        $this->invoiceType = $invoiceType;
        $this->notes = $notes;
        $this->pdfData = $pdfData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectStr,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.simple-invoice',
            with: [
                'subject' => $this->subjectStr,
            ]
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
                'PrimeLand_Invoice.pdf'
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
