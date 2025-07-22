<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get upcoming events
        $upcomingEvents = Event::with(['category', 'user'])
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        // Get recent notifications
        $recentNotifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        // Get event count
        $eventCount = Event::where('user_id', Auth::id())->count();

        // Get unread notifications count
        $unreadNotifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('home', compact(
            'upcomingEvents',
            'recentNotifications',
            'eventCount',
            'unreadNotifications'
        ));
    }

    public function likeEvent($eventId)
    {
        $event = Event::findOrFail($eventId);

        // Vérifie si l'utilisateur a déjà liké
        if (!$event->likes()->where('user_id', auth()->id())->exists()) {
            $event->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Vous aimez cet événement !');
    }
} 