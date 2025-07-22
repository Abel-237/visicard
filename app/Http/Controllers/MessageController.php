<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Events\MessageNotification;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index()
    {
        $user = Auth::user();
        $conversations = Message::with(['sender', 'receiver'])
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($message) use ($user) {
                return $message->sender_id === $user->id 
                    ? $message->receiver_id 
                    : $message->sender_id;
            });

        return view('messages.index', compact('conversations'));
    }

    /**
     * Show the chat with a specific user.
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        
        // Vérifier que l'utilisateur actuel a une carte de visite
        if (!$currentUser->businessCard) {
            return redirect()->route('business-cards.create')
                ->with('error', 'Vous devez créer une carte de visite pour pouvoir discuter avec d\'autres utilisateurs.');
        }
        
        // Vérifier que l'utilisateur destinataire a une carte de visite
        if (!$user->businessCard) {
            return redirect()->route('business-cards.index')
                ->with('error', 'Cet utilisateur n\'a pas de carte de visite.');
        }
        
        // Empêcher la conversation avec soi-même
        if ($currentUser->id === $user->id) {
            return redirect()->route('business-cards.index')
                ->with('error', 'Vous ne pouvez pas discuter avec vous-même.');
        }
        
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages comme lus
        $messages->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->each
            ->markAsRead();

        return view('messages.show', compact('user', 'messages'));
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, User $user)
    {
        $currentUser = Auth::user();
        
        // Vérifier que l'utilisateur actuel a une carte de visite
        if (!$currentUser->businessCard) {
            return response()->json([
                'error' => 'Vous devez avoir une carte de visite pour envoyer des messages.'
            ], 403);
        }
        
        // Vérifier que l'utilisateur destinataire a une carte de visite
        if (!$user->businessCard) {
            return response()->json([
                'error' => 'Cet utilisateur n\'a pas de carte de visite.'
            ], 403);
        }
        
        // Empêcher l'envoi de messages à soi-même
        if ($currentUser->id === $user->id) {
            return response()->json([
                'error' => 'Vous ne pouvez pas vous envoyer des messages à vous-même.'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $user->id,
            'content' => $validated['content'],
        ]);

        // Créer une notification dans la base de données
        $notification = Notification::create([
            'title' => 'Nouveau message de ' . $currentUser->name,
            'message' => Str::limit($validated['content'], 100),
            'type' => 'message',
            'user_id' => $user->id,
            'is_read' => false,
        ]);

        // Déclencher l'événement pour les notifications en temps réel
        event(new NewMessage($message));
        event(new MessageNotification($notification));

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message->load('sender'),
                'status' => 'success'
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé avec succès !');
    }

    /**
     * Get unread messages count.
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get new messages for real-time updates.
     */
    public function getUpdates(User $user)
    {
        $currentUser = Auth::user();
        $lastMessageId = request('last_message_id', 0);
        
        $messages = Message::with('sender')
            ->where(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $currentUser->id)
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($currentUser, $user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $currentUser->id);
            })
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark received messages as read
        $messages->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->each
            ->markAsRead();

        return response()->json([
            'messages' => $messages->load('sender'),
            'last_message_id' => $messages->max('id') ?? $lastMessageId
        ]);
    }
} 