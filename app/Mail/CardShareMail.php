<?php

namespace App\Mail;

use App\Models\CardShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CardShareMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cardShare;
    public $businessCard;
    public $user;
    public $customMessage;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct(CardShare $cardShare, $customMessage = null, $subject = null)
    {
        $this->cardShare = $cardShare;
        $this->businessCard = $cardShare->businessCard;
        $this->user = $this->businessCard->user;
        $this->customMessage = $customMessage;
        $this->subject = $subject ?: "Carte de visite virtuelle de {$this->user->name}";
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            tags: ['card-share', 'business-card'],
            metadata: [
                'card_share_id' => $this->cardShare->id,
                'business_card_id' => $this->businessCard->id,
                'user_id' => $this->user->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.card-share',
            with: [
                'recipient_name' => $this->cardShare->recipient_name ?: 'Cher(e) collègue',
                'sender_name' => $this->user->name,
                'company' => $this->businessCard->company,
                'position' => $this->businessCard->position,
                'custom_message' => $this->customMessage,
                'share_url' => $this->cardShare->getShareUrl(),
                'subject' => $this->subject,
                'business_card' => $this->businessCard,
                'user' => $this->user,
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