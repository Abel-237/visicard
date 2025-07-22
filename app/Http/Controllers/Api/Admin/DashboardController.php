<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Retourne les statistiques pour le tableau de bord administratif
     * 
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
        // Dates pour les statistiques récentes
        $lastMonth = Carbon::now()->subMonth();
        $lastWeek = Carbon::now()->subWeek();
        
        // Statistiques générales
        $totalEvents = Event::count();
        $totalUsers = User::count();
        $totalCategories = Category::count();
        $totalComments = Comment::count();
        
        // Événements publiés récemment
        $recentEvents = Event::where('published_at', '>=', $lastMonth)
            ->where('status', 'published')
            ->count();
            
        // Nouveaux utilisateurs récents
        $newUsers = User::where('created_at', '>=', $lastMonth)->count();
        
        // Commentaires récents
        $recentComments = Comment::where('created_at', '>=', $lastMonth)->count();
        
        // Événements à venir
        $upcomingEvents = Event::where('event_date', '>=', Carbon::now())
            ->where('status', 'published')
            ->count();
            
        // Répartition des événements par catégorie
        $eventsByCategory = Category::withCount('events')
            ->get()
            ->sortByDesc('events_count');
            
        // Événements les plus vus
        $popularEvents = Event::with('category')
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'views' => $event->views,
                    'category' => $event->category->name
                ];
            });
            
        // Utilisateurs les plus actifs
        $activeUsers = User::withCount(['events', 'comments'])
            ->orderByRaw('events_count + comments_count DESC')
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'events_count' => $user->events_count,
                    'comments_count' => $user->comments_count,
                    'total_activity' => $user->events_count + $user->comments_count
                ];
            });
            
        // Activité récente (événements, commentaires, utilisateurs)
        $recentActivity = DB::select("
            (SELECT 'event' as type, events.id, events.title as content, events.created_at, users.name as username
            FROM events
            JOIN users ON events.user_id = users.id
            ORDER BY events.created_at DESC
            LIMIT 5)
            
            UNION ALL
            
            (SELECT 'comment' as type, comments.id, LEFT(comments.content, 50) as content, comments.created_at, users.name as username
            FROM comments
            JOIN users ON comments.user_id = users.id
            ORDER BY comments.created_at DESC
            LIMIT 5)
            
            UNION ALL
            
            (SELECT 'user' as type, users.id, users.name as content, users.created_at, NULL as username
            FROM users
            ORDER BY users.created_at DESC
            LIMIT 5)
            
            ORDER BY created_at DESC
            LIMIT 10
        ");
        
        // Tendances des derniers événements (par semaine)
        $eventsTrend = DB::table('events')
            ->select(DB::raw('YEAR(created_at) as year, WEEK(created_at) as week, COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->map(function($item) {
                $date = Carbon::now()->setISODate($item->year, $item->week);
                return [
                    'week' => $date->startOfWeek()->format('d/m/Y'),
                    'count' => $item->count
                ];
            });
        
        return response()->json([
            'statistics' => [
                'total_events' => $totalEvents,
                'total_users' => $totalUsers,
                'total_categories' => $totalCategories,
                'total_comments' => $totalComments,
                'recent_events' => $recentEvents,
                'new_users' => $newUsers,
                'recent_comments' => $recentComments,
                'upcoming_events' => $upcomingEvents
            ],
            'events_by_category' => $eventsByCategory,
            'popular_events' => $popularEvents,
            'active_users' => $activeUsers,
            'recent_activity' => $recentActivity,
            'events_trend' => $eventsTrend
        ]);
    }
} 