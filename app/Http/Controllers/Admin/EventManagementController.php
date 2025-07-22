<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\BusinessCard;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Affiche le tableau de bord administratif des événements
     */
    public function dashboard()
    {
        $events = Event::with(['category', 'user', 'tags'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'published')->count(),
            'total_participants' => DB::table('event_user')->count(),
            'total_cards_exchanged' => BusinessCard::count(),
        ];

        return view('admin.events.dashboard', compact('events', 'stats'));
    }

    /**
     * Gère la personnalisation de l'interface d'un événement
     */
    public function customizeEvent(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $validatedData = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'theme_color' => 'nullable|string|max:7',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'custom_css' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('events/logos', 'public');
            $event->logo_path = $logoPath;
        }

        if ($request->hasFile('background_image')) {
            $bgPath = $request->file('background_image')->store('events/backgrounds', 'public');
            $event->background_image_path = $bgPath;
        }

        $event->theme_color = $validatedData['theme_color'] ?? $event->theme_color;
        $event->custom_css = $validatedData['custom_css'] ?? $event->custom_css;
        $event->save();

        return redirect()->back()->with('success', 'Interface personnalisée avec succès');
    }

    /**
     * Génère et gère les QR codes pour l'accès à l'événement
     */
    public function generateQRCodes($eventId)
    {
        $event = Event::findOrFail($eventId);
        $participants = $event->participants;
        
        $qrCodes = [];
        foreach ($participants as $participant) {
            $qrCode = QrCode::format('png')
                          ->size(300)
                          ->generate(route('events.access', ['event' => $event->id, 'user' => $participant->id]));
            
            $qrCodes[$participant->id] = $qrCode;
        }

        return view('admin.events.qr-codes', compact('event', 'qrCodes'));
    }

    /**
     * Gère les invitations des participants
     */
    public function manageInvitations(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $validatedData = $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'email',
            'message' => 'nullable|string',
        ]);

        foreach ($validatedData['emails'] as $email) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $event->participants()->attach($user->id);
                
                // Envoyer une notification
                Notification::create([
                    'user_id' => $user->id,
                    'title' => 'Invitation à un événement',
                    'message' => "Vous avez été invité à l'événement : {$event->title}",
                    'type' => 'event_invitation',
                    'data' => json_encode(['event_id' => $event->id]),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Invitations envoyées avec succès');
    }

    /**
     * Affiche les statistiques détaillées d'un événement
     */
    public function eventStats($eventId)
    {
        $event = Event::with(['participants', 'businessCards'])->findOrFail($eventId);
        
        $stats = [
            'total_participants' => $event->participants->count(),
            'active_participants' => $event->participants()
                                        ->where('last_login_at', '>=', Carbon::now()->subDay())
                                        ->count(),
            'cards_exchanged' => $event->businessCards->count(),
            'interactions' => $event->comments->count() + $event->likes->count(),
        ];

        // Statistiques par jour
        $dailyStats = DB::table('event_user')
            ->where('event_id', $eventId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.events.stats', compact('event', 'stats', 'dailyStats'));
    }

    /**
     * Exporte les données de l'événement
     */
    public function exportData($eventId)
    {
        $event = Event::with(['participants', 'businessCards', 'comments'])->findOrFail($eventId);
        
        $data = [
            'event' => $event->toArray(),
            'participants' => $event->participants->toArray(),
            'business_cards' => $event->businessCards->toArray(),
            'interactions' => [
                'comments' => $event->comments->toArray(),
                'likes' => $event->likes->toArray(),
            ],
        ];

        return response()->json($data);
    }

    /**
     * Modère les profils des participants
     */
    public function moderateProfiles(Request $request, $eventId)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:approve,reject,block',
            'reason' => 'nullable|string',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $event = Event::findOrFail($eventId);

        switch ($validatedData['action']) {
            case 'approve':
                $event->participants()->updateExistingPivot($user->id, ['status' => 'approved']);
                break;
            case 'reject':
                $event->participants()->updateExistingPivot($user->id, ['status' => 'rejected']);
                break;
            case 'block':
                $event->participants()->updateExistingPivot($user->id, ['status' => 'blocked']);
                break;
        }

        // Envoyer une notification à l'utilisateur
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Mise à jour de votre statut',
            'message' => "Votre statut pour l'événement {$event->title} a été mis à jour",
            'type' => 'profile_moderation',
            'data' => json_encode([
                'event_id' => $event->id,
                'action' => $validatedData['action'],
                'reason' => $validatedData['reason'],
            ]),
        ]);

        return redirect()->back()->with('success', 'Action de modération effectuée avec succès');
    }
} 