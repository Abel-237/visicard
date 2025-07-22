<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\Notification;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        // Marquer automatiquement les anciennes notifications de messages comme lues
        // quand l'utilisateur envoie un message (il est probablement en train de discuter)
        Notification::where('user_id', $message->sender_id)
            ->where('type', 'message')
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        // Si le message est marqué comme lu, marquer aussi les notifications correspondantes
        if ($message->wasChanged('read_at') && $message->read_at !== null) {
            Notification::where('user_id', $message->receiver_id)
                ->where('type', 'message')
                ->where('message', 'like', '%' . substr($message->content, 0, 50) . '%')
                ->update(['is_read' => true]);
        }
    }
} 