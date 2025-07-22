<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Génère un rapport des événements par période
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function eventsByPeriod(Request $request)
    {
        // Validation des entrées
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'period' => 'required|in:daily,weekly,monthly,yearly',
            'category' => 'nullable|exists:categories,id',
        ]);

        // Définition des dates par défaut si non fournies
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $period = $request->period ?? 'monthly';
        $categoryId = $request->category;

        // Construction de la requête de base
        $query = Event::whereBetween('published_at', [$startDate, $endDate]);
        
        // Filtrer par catégorie si spécifiée
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Groupement des données selon la période sélectionnée
        switch ($period) {
            case 'daily':
                $data = $query->select(DB::raw('DATE(published_at) as date'), DB::raw('COUNT(*) as count'))
                            ->groupBy(DB::raw('DATE(published_at)'))
                            ->orderBy('date')
                            ->get();
                $format = 'd/m/Y';
                break;
            case 'weekly':
                $data = $query->select(DB::raw('YEARWEEK(published_at) as yearweek'), DB::raw('COUNT(*) as count'))
                            ->groupBy('yearweek')
                            ->orderBy('yearweek')
                            ->get()
                            ->map(function ($item) {
                                // Convertir YEARWEEK en date lisible (premier jour de la semaine)
                                $year = substr($item->yearweek, 0, 4);
                                $week = substr($item->yearweek, 4);
                                $date = Carbon::now()->setISODate($year, $week)->startOfWeek();
                                return [
                                    'date' => $date->format('d/m/Y'),
                                    'count' => $item->count
                                ];
                            });
                $format = 'd/m/Y';
                break;
            case 'yearly':
                $data = $query->select(DB::raw('YEAR(published_at) as year'), DB::raw('COUNT(*) as count'))
                            ->groupBy(DB::raw('YEAR(published_at)'))
                            ->orderBy('year')
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'date' => $item->year,
                                    'count' => $item->count
                                ];
                            });
                $format = 'Y';
                break;
            case 'monthly':
            default:
                $data = $query->select(DB::raw('YEAR(published_at) as year'), DB::raw('MONTH(published_at) as month'), DB::raw('COUNT(*) as count'))
                            ->groupBy(DB::raw('YEAR(published_at)'), DB::raw('MONTH(published_at)'))
                            ->orderBy('year')
                            ->orderBy('month')
                            ->get()
                            ->map(function ($item) {
                                $date = Carbon::createFromDate($item->year, $item->month, 1);
                                return [
                                    'date' => $date->format('m/Y'),
                                    'count' => $item->count
                                ];
                            });
                $format = 'm/Y';
                break;
        }

        // Préparation des données pour le graphique
        $chartLabels = $data->pluck('date');
        $chartData = $data->pluck('count');

        // Récupération des catégories pour le filtre
        $categories = Category::all();

        return response()->json([
            'data' => $data, 
            'chartLabels' => $chartLabels, 
            'chartData' => $chartData, 
            'startDate' => $startDate->toDateString(), 
            'endDate' => $endDate->toDateString(), 
            'period' => $period, 
            'categoryId' => $categoryId, 
            'categories' => $categories,
            'format' => $format
        ]);
    }

    /**
     * Génère un rapport de participation aux événements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function participation(Request $request)
    {
        // Validation des entrées
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'sort' => 'nullable|in:views,comments,likes',
        ]);

        // Définition des dates par défaut si non fournies
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(3);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $sort = $request->sort ?? 'views';

        // Récupération des événements dans la période
        $eventsQuery = Event::with(['category', 'comments', 'likes'])
            ->where('status', 'published')
            ->whereBetween('published_at', [$startDate, $endDate]);
        
        // Tri selon le critère sélectionné
        switch ($sort) {
            case 'comments':
                $eventsQuery->withCount('comments')
                            ->orderBy('comments_count', 'desc');
                break;
            case 'likes':
                $eventsQuery->withCount('likes')
                            ->orderBy('likes_count', 'desc');
                break;
            case 'views':
            default:
                $eventsQuery->orderBy('views', 'desc');
                break;
        }

        $events = $eventsQuery->limit(20)->get();

        // Statistiques globales
        $totalViews = $events->sum('views');
        $totalComments = Comment::whereIn('event_id', $events->pluck('id'))->count();
        $totalLikes = Like::whereHasMorph('likeable', Event::class, function($query) use ($events) {
            $query->whereIn('id', $events->pluck('id'));
        })->count();
        
        // Préparation des données pour les graphiques
        $eventsNames = $events->pluck('title');
        $eventsViews = $events->pluck('views');
        $eventsComments = $events->map(function($event) {
            return $event->comments->count();
        });
        $eventsLikes = $events->map(function($event) {
            return $event->likes->count();
        });

        return response()->json([
            'events' => $events, 
            'startDate' => $startDate->toDateString(), 
            'endDate' => $endDate->toDateString(), 
            'sort' => $sort,
            'totalViews' => $totalViews,
            'totalComments' => $totalComments,
            'totalLikes' => $totalLikes,
            'eventsNames' => $eventsNames,
            'eventsViews' => $eventsViews,
            'eventsComments' => $eventsComments,
            'eventsLikes' => $eventsLikes
        ]);
    }

    /**
     * Génère un rapport d'analyse des tendances et préférences utilisateurs
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trends(Request $request)
    {
        // Définition des dates par défaut si non fournies
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Catégories les plus populaires
        $popularCategories = Category::withCount(['events' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('published_at', [$startDate, $endDate])
                      ->where('status', 'published');
            }])
            ->having('events_count', '>', 0)
            ->orderBy('events_count', 'desc')
            ->get();

        // Tags les plus utilisés
        $popularTags = DB::table('tags')
            ->join('event_tag', 'tags.id', '=', 'event_tag.tag_id')
            ->join('events', 'event_tag.event_id', '=', 'events.id')
            ->where('events.status', 'published')
            ->whereBetween('events.published_at', [$startDate, $endDate])
            ->select('tags.name', DB::raw('COUNT(event_tag.event_id) as count'))
            ->groupBy('tags.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Utilisateurs les plus actifs (commentaires)
        $activeUsers = User::withCount(['comments' => function($query) use ($startDate, $endDate) {
                $query->whereHas('event', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('published_at', [$startDate, $endDate])
                      ->where('status', 'published');
                });
            }])
            ->having('comments_count', '>', 0)
            ->orderBy('comments_count', 'desc')
            ->limit(10)
            ->get();

        // Heures de publication les plus fréquentes
        $popularHours = Event::where('status', 'published')
            ->whereBetween('published_at', [$startDate, $endDate])
            ->select(DB::raw('HOUR(published_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();

        // Préparation des données pour les graphiques
        $categoryNames = $popularCategories->pluck('name');
        $categoryColors = $popularCategories->pluck('color')->map(function($color) {
            return $color ?? 'primary'; // Couleur par défaut si null
        });
        $categoryEventCounts = $popularCategories->pluck('events_count');
        
        $tagNames = $popularTags->pluck('name');
        $tagCounts = $popularTags->pluck('count');
        
        $userNames = $activeUsers->pluck('name');
        $userCommentCounts = $activeUsers->pluck('comments_count');
        
        $hours = $popularHours->pluck('hour');
        $hourCounts = $popularHours->pluck('count');

        return response()->json([
            'popularCategories' => $popularCategories,
            'popularTags' => $popularTags,
            'activeUsers' => $activeUsers,
            'popularHours' => $popularHours,
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'categoryNames' => $categoryNames,
            'categoryColors' => $categoryColors,
            'categoryEventCounts' => $categoryEventCounts,
            'tagNames' => $tagNames,
            'tagCounts' => $tagCounts,
            'userNames' => $userNames,
            'userCommentCounts' => $userCommentCounts,
            'hours' => $hours,
            'hourCounts' => $hourCounts
        ]);
    }
} 