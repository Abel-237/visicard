<?php

namespace App\Http\Controllers;

use App\Events\NewNotification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
                                  ->with(['event', 'sender'])
                                  ->latest()
                                  ->paginate(20);
        
        // Marquer automatiquement toutes les notifications comme lues
        Notification::where('user_id', Auth::id())
                  ->where('is_read', false)
                  ->update(['is_read' => true]);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                           ->with('error', 'Vous n\'avez pas la permission de modifier cette notification');
        }
        
        $notification->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'Notification marquée comme lue');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
                  ->where('is_read', false)
                  ->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user owns this notification
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                           ->with('error', 'Vous n\'avez pas la permission de supprimer cette notification');
        }
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification supprimée avec succès');
    }
    
    /**
     * Clear all notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        
        return redirect()->route('notifications.index')
                       ->with('success', 'Toutes les notifications ont été supprimées');
    }
    
    /**
     * Update notification preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePreferences(Request $request)
    {
        $validatedData = $request->validate([
            'notification_preferences' => 'required|boolean',
        ]);
        
        User::where('id', Auth::id())
            ->update(['notification_preferences' => $validatedData['notification_preferences']]);
        
        return redirect()->back()->with('success', 'Préférences de notification mises à jour avec succès');
    }
    
    /**
     * Get unread notifications count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
                           ->where('is_read', false)
                           ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Create a new notification.
     *
     * @param array $data
     * @return Notification
     */
    public function createNotification(array $data)
    {
        $notification = Notification::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'user_id' => $data['user_id'],
            'sender_id' => $data['sender_id'] ?? null,
            'event_id' => $data['event_id'] ?? null,
            'is_read' => false,
        ]);
        
        // Broadcast notification event
        event(new NewNotification($notification));
        
        return $notification;
    }
    
    /**
     * Get unread notifications for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
                                 ->where('is_read', false)
                                 ->with(['event', 'sender'])
                                 ->latest()
                                 ->take(10)
                                 ->get();
        
        return response()->json($notifications);
    }
    
    /**
     * Send a test notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestNotification(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:message,event,reminder,system'
        ]);
        
        try {
            $notification = $this->createNotification([
                'title' => $validatedData['title'],
                'message' => $validatedData['message'],
                'type' => $validatedData['type'],
                'user_id' => Auth::id(),
                'sender_id' => $validatedData['type'] === 'message' ? Auth::id() : null,
                'event_id' => null
            ]);
            
            return redirect()->route('notifications.index')
                           ->with('success', 'Notification de test envoyée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
        }
    }
}
