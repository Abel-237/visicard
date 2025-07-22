<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Display the search form and results.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get search parameters
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $tag = $request->input('tag');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $location = $request->input('location');
        
        // Initialize query
        $query = Event::with(['category', 'user', 'tags', 'media'])
                     ->published();
        
        // Apply keyword filter
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%")
                  ->orWhere('excerpt', 'like', "%{$keyword}%")
                  ->orWhere('location', 'like', "%{$keyword}%");
            });
        }
        
        // Apply category filter
        if ($category) {
            $query->where('category_id', $category);
        }
        
        // Apply tag filter
        if ($tag) {
            $query->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag);
            });
        }
        
        // Apply date filters
        if ($dateFrom) {
            $fromDate = Carbon::parse($dateFrom)->startOfDay();
            $query->where('event_date', '>=', $fromDate);
        }
        
        if ($dateTo) {
            $toDate = Carbon::parse($dateTo)->endOfDay();
            $query->where('event_date', '<=', $toDate);
        }
        
        // Apply location filter
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }
        
        // Get results
        $events = $query->latest('published_at')->paginate(12);
        
        // Load categories and tags for search form
        $categories = Category::all();
        $tags = Tag::all();
        
        // Return view with results
        return view('search.index', compact(
            'events',
            'categories',
            'tags',
            'keyword',
            'category',
            'tag',
            'dateFrom',
            'dateTo',
            'location'
        ));
    }
    
    /**
     * Quick search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function quickSearch(Request $request)
    {
        $q = $request->input('q');
        
        if (!$q) {
            return redirect()->route('home');
        }
        
        // Initialize query
        $query = Event::with(['category', 'user', 'tags'])
                     ->published();
        
        // Apply search term
        $query->where(function($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('content', 'like', "%{$query}%")
              ->orWhere('excerpt', 'like', "%{$query}%")
              ->orWhere('location', 'like', "%{$query}%");
        });
        
        // Get results
        $events = $query->latest('published_at')->paginate(12);
        
        return view('search.quick', compact('events', 'q'));
    }
}
