<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Affiche la liste des événements
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get sort parameter
        $sort = $request->input('sort', 'latest');
        
        // Get category filter
        $categoryId = $request->input('category');
        
        // Get tag filter
        $tagId = $request->input('tag');
        
        // Base query for regular events
        $eventsQuery = Event::with(['category', 'user', 'media'])
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
        
        // Get the events with pagination
        $events = $eventsQuery->paginate(12);
        
        return response()->json([
            'events' => $events,
            'filters' => [
                'sort' => $sort,
                'category' => $categoryId,
                'tag' => $tagId
            ]
        ]);
    }

    /**
     * Affiche les événements à la une
     * 
     * @return \Illuminate\Http\Response
     */
    public function featured()
    {
        $featuredEvents = Event::with(['category', 'media', 'tags'])
            ->where('featured', true)
            ->where('status', 'published')
            ->latest('published_at')
            ->take(5)
            ->get();
            
        return response()->json($featuredEvents);
    }

    /**
     * Affiche les détails d'un événement
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $event = Event::with(['category', 'user', 'media', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
            
        // Incrémenter le compteur de vues
        $event->increment('views');
        
        // Vérifier si l'utilisateur a aimé cet événement
        $isLiked = false;
        if (Auth::check()) {
            $isLiked = $event->likes()->where('user_id', Auth::id())->exists();
        }
        
        return response()->json([
            'event' => $event,
            'isLiked' => $isLiked
        ]);
    }

    /**
     * Affiche les événements par catégorie
     * 
     * @param  int  $category
     * @return \Illuminate\Http\Response
     */
    public function byCategory($category)
    {
        $events = Event::with(['category', 'user', 'media'])
            ->where('category_id', $category)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(12);
            
        $categoryInfo = Category::findOrFail($category);
            
        return response()->json([
            'events' => $events,
            'category' => $categoryInfo
        ]);
    }

    /**
     * Affiche les événements par tag
     * 
     * @param  int  $tag
     * @return \Illuminate\Http\Response
     */
    public function byTag($tag)
    {
        $events = Event::with(['category', 'user', 'media'])
            ->whereHas('tags', function($q) use ($tag) {
                $q->where('tags.id', $tag);
            })
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(12);
            
        $tagInfo = \App\Models\Tag::findOrFail($tag);
            
        return response()->json([
            'events' => $events,
            'tag' => $tagInfo
        ]);
    }

    /**
     * Crée un nouvel événement
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'required|date',
            'location' => 'nullable|max:255',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Création de l'événement
        $event = new Event();
        $event->title = $request->title;
        $event->content = $request->content;
        $event->excerpt = $request->excerpt;
        $event->category_id = $request->category_id;
        $event->user_id = Auth::id();
        $event->event_date = $request->event_date;
        $event->location = $request->location;
        $event->status = $request->input('status', 'draft');
        $event->featured = $request->has('featured') ? true : false;
        $event->published_at = $event->status === 'published' ? now() : null;
        $event->save();
        
        // Traiter les médias
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('events/media', 'public');
                $event->media()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $this->getFileType($file->getMimeType()),
                    'file_size' => $file->getSize()
                ]);
            }
        }
        
        // Charger les relations pour la réponse
        $event->load(['category', 'user', 'media']);
        
        return response()->json($event, 201);
    }

    /**
     * Met à jour un événement
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Vérification des autorisations
        if (Auth::id() !== $event->user_id && !Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'required|date',
            'location' => 'nullable|max:255',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Mise à jour de l'événement
        $event->title = $request->title;
        $event->content = $request->content;
        $event->excerpt = $request->excerpt;
        $event->category_id = $request->category_id;
        $event->event_date = $request->event_date;
        $event->location = $request->location;
        
        // Mise à jour du statut si différent
        if ($request->has('status') && $request->status !== $event->status) {
            $event->status = $request->status;
            if ($event->status === 'published' && !$event->published_at) {
                $event->published_at = now();
            }
        }
        
        $event->featured = $request->has('featured') ? true : false;
        $event->save();
        
        // Traiter les médias si présents
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('events/media', 'public');
                $event->media()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $this->getFileType($file->getMimeType()),
                    'file_size' => $file->getSize()
                ]);
            }
        }
        
        // Charger les relations pour la réponse
        $event->load(['category', 'user', 'media']);
        
        return response()->json($event);
    }

    /**
     * Supprime un événement
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Vérification des autorisations
        if (Auth::id() !== $event->user_id && !Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        // Suppression de l'événement et de ses relations
        $event->delete();
        
        return response()->json(['message' => 'Event deleted successfully']);
    }

    /**
     * Ajoute un commentaire à un événement
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|min:3'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
            
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->event_id = $event->id;
        $comment->content = $request->content;
        $comment->save();
        
        // Charger l'utilisateur pour la réponse
        $comment->load('user');
        
        return response()->json($comment, 201);
    }

    /**
     * Aime ou n'aime plus un événement
     * 
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function toggleLike($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
            
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a déjà aimé cet événement
        $like = $event->likes()->where('user_id', $user->id)->first();
        
        if ($like) {
            // Si déjà aimé, supprimer le like
            $like->delete();
            $liked = false;
        } else {
            // Sinon, ajouter un like
            $event->likes()->create([
                'user_id' => $user->id
            ]);
            $liked = true;
        }
        
        // Réponse avec le nombre actuel de likes
        return response()->json([
            'likes_count' => $event->likes()->count(),
            'liked' => $liked
        ]);
    }
    
    /**
     * Détermine le type de fichier en fonction du MIME type
     * 
     * @param  string  $mimeType
     * @return string
     */
    private function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'application/pdf')) {
            return 'pdf';
        } elseif (str_starts_with($mimeType, 'application/msword') || 
                  str_starts_with($mimeType, 'application/vnd.openxmlformats-officedocument.wordprocessingml')) {
            return 'document';
        } else {
            return 'other';
        }
    }
} 