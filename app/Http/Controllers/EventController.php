<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Tag;
use App\Models\Media;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get all categories for filtering
        $categories = Category::all();
        
        // Base query for published events
        $eventsQuery = Event::with(['category', 'user'])
                          ->published()
                          ->orderBy('published_at', 'desc');
        
        // Filter by category if requested
        $categoryId = $request->input('category');
        if ($categoryId) {
            $eventsQuery->where('category_id', $categoryId);
        }
        
        // Filter by tag if requested
        $tagId = $request->input('tag');
        if ($tagId) {
            $eventsQuery->whereHas('tags', function($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }
        
        // Search by keyword if provided
        $keyword = $request->input('search');
        if ($keyword) {
            $eventsQuery->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%'.$keyword.'%')
                  ->orWhere('content', 'like', '%'.$keyword.'%')
                  ->orWhere('excerpt', 'like', '%'.$keyword.'%');
            });
        }
        
        // Sort by date or views
        $sort = $request->input('sort', 'latest');
        if ($sort == 'latest') {
            $eventsQuery->orderBy('published_at', 'desc');
        } elseif ($sort == 'oldest') {
            $eventsQuery->orderBy('published_at', 'asc');
        } elseif ($sort == 'popular') {
            $eventsQuery->orderBy('views', 'desc');
        } elseif ($sort == 'upcoming') {
            $eventsQuery->where('event_date', '>=', now())
                       ->orderBy('event_date', 'asc');
        }
        
        // Get all published events (paginated)
        $events = $eventsQuery->paginate(12);
        
        return view('events.index', compact('events', 'categories', 'categoryId', 'tagId', 'keyword', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user is admin or editor
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor') {
            return redirect()->route('events.index')
                           ->with('error', 'Vous n\'avez pas la permission de créer un événement');
        }
        
        $categories = Category::all();
        
        return view('events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user is admin or editor
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor') {
            return redirect()->route('events.index')
                           ->with('error', 'Vous n\'avez pas la permission de créer un événement');
        }
        
        // Correction : convertir les tags string en array si besoin
        if ($request->filled('tags') && is_string($request->tags)) {
            $request->merge([
                'tags' => array_filter(array_map('trim', explode(',', $request->tags)))
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'nullable|date',
            'location' => 'nullable|max:255',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,pdf,doc,docx|max:10240',
        ]);
        
        // Create a slug from the title
        $slug = Str::slug($validatedData['title']);
        
        // Check if slug exists
        $count = Event::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
        
        // Create the event
        $event = new Event();
        $event->title = $validatedData['title'];
        $event->slug = $slug;
        $event->content = $validatedData['content'];
        $event->excerpt = $validatedData['excerpt'] ?? null;
        $event->category_id = $validatedData['category_id'];
        $event->user_id = Auth::id();
        $event->event_date = $validatedData['event_date'] ?? null;
        $event->location = $validatedData['location'] ?? null;
        $event->featured = $request->has('featured');
        $event->status = $validatedData['status'];
        // Gestion de la date de publication différée
        if ($validatedData['status'] == 'published') {
            $event->published_at = $request->filled('published_at') ? $request->input('published_at') : now();
        } else {
            $event->published_at = null;
        }
        $event->save();
        
        // Attach tags if any
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        }
        
        // Upload media files if any
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('events/' . $event->id, $filename, 'public');
                
                $fileType = 'document';
                if (in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])) {
                    $fileType = 'image';
                } elseif (in_array($file->getClientMimeType(), ['video/mp4'])) {
                    $fileType = 'video';
                }
                
                $media = new Media();
                $media->name = $file->getClientOriginalName();
                $media->file_path = $filepath;
                $media->file_type = $fileType;
                $media->mime_type = $file->getClientMimeType();
                $media->size = $file->getSize();
                $media->event_id = $event->id;
                $media->uploaded_by = Auth::id();
                $media->save();
            }
        }
        
        // Notify users who have preferences for this category
        $this->notifyUsers($event);
        
        return redirect()->route('events.show', $event->slug)
                       ->with('success', 'Événement créé avec succès!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $event = Event::with(['category', 'user', 'tags', 'media', 'comments.user', 'comments.replies.user'])
                    ->where('slug', $slug)
                    ->firstOrFail();
        
        // If event is not published and user is not admin/editor/owner, abort
        if (!$event->isPublished() && 
            (!Auth::check() || 
             (Auth::id() != $event->user_id && 
              Auth::user()->role !== 'admin' && 
              Auth::user()->role !== 'editor'))) {
            abort(404);
        }
        
        // Increment view count
        $event->incrementViewCount();
        
        // Get related events from same category
        $relatedEvents = Event::with(['category', 'user'])
                            ->published()
                            ->where('category_id', $event->category_id)
                            ->where('id', '!=', $event->id)
                            ->take(4)
                            ->get();
        
        // Get comments
        $comments = $event->comments()
                        ->approved()
                        ->parent()
                        ->with(['user', 'replies.user'])
                        ->latest()
                        ->get();
        
        return view('events.show', compact('event', 'relatedEvents', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        $event = Event::with(['category', 'tags', 'media'])
                    ->where('slug', $slug)
                    ->firstOrFail();
        
        // Check if user is admin or editor or owner
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor' && Auth::id() != $event->user_id) {
            return redirect()->route('events.index')
                           ->with('error', 'Vous n\'avez pas la permission de modifier cet événement');
        }
        
        $categories = Category::all();
        
        return view('events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Check if user is admin or editor or owner
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor' && Auth::id() != $event->user_id) {
            return redirect()->route('events.index')
                           ->with('error', 'Vous n\'avez pas la permission de modifier cet événement');
        }
        
        // Correction : convertir les tags string en array si besoin
        if ($request->filled('tags') && is_string($request->tags)) {
            $request->merge([
                'tags' => array_filter(array_map('trim', explode(',', $request->tags)))
            ]);
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'nullable|date',
            'location' => 'nullable|max:255',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'exists:media,id',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,svg,mp4,pdf,doc,docx|max:10240',
        ]);
        
        // Create a new slug if title changed
        if ($event->title != $validatedData['title']) {
            $slug = Str::slug($validatedData['title']);
            
            // Check if slug exists
            $count = Event::where('slug', $slug)->where('id', '!=', $event->id)->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
            
            $event->slug = $slug;
        }
        
        // Update the event
        $event->title = $validatedData['title'];
        $event->content = $validatedData['content'];
        $event->excerpt = $validatedData['excerpt'] ?? null;
        $event->category_id = $validatedData['category_id'];
        $event->event_date = $validatedData['event_date'] ?? null;
        $event->location = $validatedData['location'] ?? null;
        $event->featured = $request->has('featured');
        
        // Gestion de la date de publication différée lors de la mise à jour
        if ($validatedData['status'] == 'published') {
            $event->published_at = $request->filled('published_at') ? $request->input('published_at') : now();
        } else {
            $event->published_at = null;
        }
        
        $event->status = $validatedData['status'];
        $event->save();
        
        // Sync tags
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags);
        } else {
            $event->tags()->detach();
        }
        
        // Delete media if requested
        if ($request->has('delete_media')) {
            foreach ($request->delete_media as $mediaId) {
                $media = Media::find($mediaId);
                if ($media && $media->event_id == $event->id) {
                    // Delete file from storage
                    if (Storage::disk('public')->exists($media->file_path)) {
                        Storage::disk('public')->delete($media->file_path);
                    }
                    
                    // Delete from database
                    $media->delete();
                }
            }
        }
        
        // Upload new media files if any
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $filepath = $file->storeAs('events/' . $event->id, $filename, 'public');
                
                $fileType = 'document';
                if (in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'])) {
                    $fileType = 'image';
                } elseif (in_array($file->getClientMimeType(), ['video/mp4'])) {
                    $fileType = 'video';
                }
                
                $media = new Media();
                $media->name = $file->getClientOriginalName();
                $media->file_path = $filepath;
                $media->file_type = $fileType;
                $media->mime_type = $file->getClientMimeType();
                $media->size = $file->getSize();
                $media->event_id = $event->id;
                $media->uploaded_by = Auth::id();
                $media->save();
            }
        }
        
        return redirect()->route('events.show', $event->slug)
                       ->with('success', 'Événement mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Check if user is admin or editor or owner
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor' && Auth::id() != $event->user_id) {
            return redirect()->route('events.index')
                           ->with('error', 'Vous n\'avez pas la permission de supprimer cet événement');
        }
        
        // Delete media files
        foreach ($event->media as $media) {
            if (Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }
        }
        
        // Delete the event (cascading will delete comments, tags, media)
        $event->delete();
        
        return redirect()->route('events.index')
                       ->with('success', 'Événement supprimé avec succès!');
    }
    
    /**
     * Search for events.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
        
        if (!$keyword) {
            return redirect()->route('events.index');
        }
        
        $events = Event::with(['category', 'user', 'tags'])
                     ->published()
                     ->where('title', 'like', '%'.$keyword.'%')
                     ->orWhere('content', 'like', '%'.$keyword.'%')
                     ->orWhere('excerpt', 'like', '%'.$keyword.'%')
                     ->orWhereHas('tags', function($q) use ($keyword) {
                         $q->where('name', 'like', '%'.$keyword.'%');
                     })
                     ->latest('published_at')
                     ->paginate(12);
        
        return view('events.search', compact('events', 'keyword'));
    }
    
    /**
     * Add a comment to an event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function addComment(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Validate the request
        $validatedData = $request->validate([
            'content' => 'required|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        
        // Create the comment
        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->event_id = $event->id;
        $comment->user_id = Auth::id();
        
        if ($request->has('parent_id')) {
            $comment->parent_id = $validatedData['parent_id'];
        }
        
        $comment->save();
        
        // Notify the event author
        if ($event->user_id != Auth::id()) {
            $notificationController = app(NotificationController::class);
            
            $notificationData = [
                'title' => 'Nouveau commentaire',
                'message' => Auth::user()->name . ' a commenté votre événement "' . $event->title . '"',
                'type' => 'comment',
                'user_id' => $event->user_id,
                'event_id' => $event->id,
            ];
            
            $notificationController->createNotification($notificationData);
        }
        
        return redirect()->back()->with('success', 'Commentaire ajouté avec succès!');
    }
    
    /**
     * Like/unlike an event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function toggleLike($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        // Check if user already liked this event
        $like = $event->likes()->where('user_id', Auth::id())->first();
        
        if ($like) {
            // Unlike
            $like->delete();
            $message = 'Vous n\'aimez plus cet événement';
        } else {
            // Like
            $event->likes()->create([
                'user_id' => Auth::id(),
            ]);
            $message = 'Vous aimez cet événement';

            // Notify the event author if not the same as liker
            if ($event->user_id != Auth::id()) {
                $notificationController = app(NotificationController::class);
                
                // Vérifier les préférences de notification de l'auteur
                $author = $event->user;
                $pref = $author->notificationPreference;
                
                if ($pref && in_array('like', (array)($pref->types ?? []))) {
                    $notificationData = [
                        'title' => 'Nouveau like',
                        'message' => Auth::user()->name . ' aime votre événement "' . $event->title . '"',
                        'type' => 'like',
                        'user_id' => $event->user_id,
                        'event_id' => $event->id,
                    ];
                    
                    $notificationController->createNotification($notificationData);
                }
            }
        }
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Notify users about a new event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    private function notifyUsers($event)
    {
        // Only notify users if event is published
        if ($event->status != 'published') {
            return;
        }

        // Get users with notification preferences enabled
        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
        $notificationController = app(NotificationController::class);

        foreach ($users as $user) {
            $pref = $user->notificationPreference;
            // Si l'utilisateur n'a pas de préférence, on ne notifie pas
            if (!$pref) continue;
            // Vérifie la catégorie
            if (!empty($pref->categories) && !in_array($event->category_id, $pref->categories)) continue;
            // Vérifie le type d'alerte
            if (!empty($pref->types) && !in_array('event', $pref->types)) continue;

            $notificationData = [
                'title' => 'Nouvel événement',
                'message' => 'Un nouvel événement a été publié: "' . $event->title . '"',
                'type' => 'event',
                'user_id' => $user->id,
                'event_id' => $event->id,
            ];
            $notificationController->createNotification($notificationData);
        }
    }

    /**
     * Export event to PDF
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($slug)
    {
        // Get the event
        $event = Event::with(['category', 'user', 'tags', 'media', 'comments' => function($query) {
            $query->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }])
        ->where('slug', $slug)
        ->firstOrFail();

        // Check if event is published or user is the owner or admin
        if (!$event->isPublished() && 
            (!Auth::check() || (Auth::id() != $event->user_id && Auth::user()->role != 'admin'))) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        // Create PDF
        $pdf = PDF::loadView('events.pdf', compact('event'));
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Download the PDF
        return $pdf->download($event->slug . '.pdf');
    }
}
