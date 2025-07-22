<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Recherche dans les événements, catégories et tags
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return response()->json([
                'events' => [],
                'categories' => [],
                'tags' => []
            ]);
        }
        
        // Rechercher dans les événements
        $events = Event::where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%")
                  ->orWhere('location', 'LIKE', "%{$query}%");
            })
            ->with(['category', 'user', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(10);
        
        // Rechercher dans les catégories
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->withCount(['events' => function($q) {
                $q->where('status', 'published');
            }])
            ->get();
        
        // Rechercher dans les tags
        $tags = Tag::where('name', 'LIKE', "%{$query}%")
            ->withCount('events')
            ->get();
        
        return response()->json([
            'events' => $events,
            'categories' => $categories,
            'tags' => $tags,
            'query' => $query
        ]);
    }
} 