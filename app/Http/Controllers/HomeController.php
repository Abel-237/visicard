<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Get all categories with count of published events (no pagination)
        $categories = Category::withCount(['events' => function ($query) {
            $query->published();
        }])->get();

        // Get featured events for carousel
        $featuredEvents = Event::with(['category', 'media', 'tags'])
            ->where('featured', true)
            ->where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get sort parameter
        $sort = $request->input('sort', 'latest');
        
        // Get category filter
        $categoryId = $request->input('category');
        
        // Get tag filter
        $tagId = $request->input('tag');
        
        // Base query for regular events
        $eventsQuery = Event::with(['category', 'user', 'tags', 'media'])
            ->published();
        
        // Apply category filter if present
        if ($categoryId) {
            $eventsQuery->where('category_id', $categoryId);
        }
        
        // Apply tag filter if present
        if ($tagId) {
            $eventsQuery->whereHas('tags', function($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $eventsQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $eventsQuery->orderBy('views', 'desc');
                break;
            case 'upcoming':
                $eventsQuery->where('event_date', '>=', Carbon::now())
                           ->orderBy('event_date', 'asc');
                break;
            default: // latest
                $eventsQuery->latest('published_at');
                break;
        }
        
        // Get the events
        $events = $eventsQuery->paginate(12);
        
        // Get upcoming events for sidebar
        $upcomingEvents = Event::with('category')
            ->published()
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();
        
        // Get counts for stats
        $stats = [
            'totalEvents' => Event::published()->count(),
            'upcomingEvents' => Event::published()->where('event_date', '>=', Carbon::now())->count(),
            'categories' => Category::count(),
        ];
        
        return view('welcome', compact(
            'featuredEvents', 
            'events', 
            'categories', 
            'categoryId', 
            'tagId',
            'sort',
            'upcomingEvents',
            'stats'
        ));
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Show the contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('contact');
    }
}
