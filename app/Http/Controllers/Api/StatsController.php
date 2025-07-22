<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Retourne les statistiques générales
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stats = [
            'totalEvents' => Event::published()->count(),
            'upcomingEvents' => Event::published()->where('event_date', '>=', Carbon::now())->count(),
            'categories' => Category::count(),
            'popularEvents' => Event::published()
                ->orderBy('views', 'desc')
                ->with('category')
                ->take(5)
                ->get()
                ->map(function($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'slug' => $event->slug,
                        'views' => $event->views,
                        'category' => [
                            'id' => $event->category->id,
                            'name' => $event->category->name,
                            'color' => $event->category->color,
                        ]
                    ];
                }),
            'recentComments' => \App\Models\Comment::with(['user', 'event'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => \Illuminate\Support\Str::limit($comment->content, 100),
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                        ],
                        'event' => [
                            'id' => $comment->event->id,
                            'title' => $comment->event->title,
                            'slug' => $comment->event->slug,
                        ]
                    ];
                }),
            'categoryDistribution' => Category::withCount(['events' => function($query) {
                    $query->published();
                }])
                ->having('events_count', '>', 0)
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'color' => $category->color,
                        'count' => $category->events_count
                    ];
                })
        ];
        
        return response()->json($stats);
    }
} 