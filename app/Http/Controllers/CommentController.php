<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only admin can view all comments
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')
                           ->with('error', 'Vous n\'avez pas la permission d\'accéder à cette page');
        }
        
        $comments = Comment::with(['user', 'event'])
                         ->latest()
                         ->paginate(20);
                         
        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request
        $validatedData = $request->validate([
            'content' => 'required|max:1000',
            'event_id' => 'required|exists:events,id',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        
        $event = Event::findOrFail($validatedData['event_id']);
        
        // Create comment
        $comment = new Comment();
        $comment->content = $validatedData['content'];
        $comment->event_id = $validatedData['event_id'];
        $comment->user_id = Auth::id();
        
        if ($request->has('parent_id')) {
            $comment->parent_id = $validatedData['parent_id'];
        }
        
        $comment->save();
        
        $notificationController = app(NotificationController::class);
        
        // Notify the event author if not the same as commenter
        if ($event->user_id != Auth::id()) {
            $author = $event->user;
            $pref = $author->notificationPreference;
            
            if ($pref && in_array('comment', (array)($pref->types ?? []))) {
                $notificationData = [
                    'title' => 'Nouveau commentaire',
                    'message' => Auth::user()->name . ' a commenté votre événement "' . $event->title . '"',
                    'type' => 'comment',
                    'user_id' => $event->user_id,
                    'event_id' => $event->id,
                ];
                
                $notificationController->createNotification($notificationData);
            }
        }
        
        // If this is a reply, notify the parent comment author
        if ($request->has('parent_id')) {
            $parentComment = Comment::findOrFail($validatedData['parent_id']);
            
            if ($parentComment->user_id != Auth::id()) {
                $parentAuthor = $parentComment->user;
                $pref = $parentAuthor->notificationPreference;
                
                if ($pref && in_array('comment', (array)($pref->types ?? []))) {
                    $notificationData = [
                        'title' => 'Réponse à votre commentaire',
                        'message' => Auth::user()->name . ' a répondu à votre commentaire sur l\'événement "' . $event->title . '"',
                        'type' => 'comment',
                        'user_id' => $parentComment->user_id,
                        'event_id' => $event->id,
                    ];
                    
                    $notificationController->createNotification($notificationData);
                }
            }
        } else {
            // Notify other commenters of the event (except the current user and event author)
            $otherCommenters = Comment::where('event_id', $event->id)
                ->where('user_id', '!=', Auth::id())
                ->where('user_id', '!=', $event->user_id)
                ->distinct('user_id')
                ->pluck('user_id');
            
            foreach ($otherCommenters as $commenterId) {
                $commenter = User::find($commenterId);
                $pref = $commenter->notificationPreference;
                
                if ($pref && in_array('comment', (array)($pref->types ?? []))) {
                    $notificationData = [
                        'title' => 'Nouveau commentaire',
                        'message' => Auth::user()->name . ' a commenté l\'événement "' . $event->title . '"',
                        'type' => 'comment',
                        'user_id' => $commenterId,
                        'event_id' => $event->id,
                    ];
                    
                    $notificationController->createNotification($notificationData);
                }
            }
        }
        
        return redirect()->back()->with('success', 'Commentaire ajouté avec succès!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Check if user is authorized to edit this comment
        if (Auth::user()->role !== 'admin' && Auth::id() !== $comment->user_id) {
            return redirect()->back()
                           ->with('error', 'Vous n\'avez pas la permission de modifier ce commentaire');
        }
        
        // Validate request
        $validatedData = $request->validate([
            'content' => 'required|max:1000',
        ]);
        
        // Update comment
        $comment->content = $validatedData['content'];
        $comment->save();
        
        return redirect()->back()->with('success', 'Commentaire mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        
        // Check if user is authorized to delete this comment
        if (Auth::user()->role !== 'admin' && Auth::id() !== $comment->user_id) {
            return redirect()->back()
                           ->with('error', 'Vous n\'avez pas la permission de supprimer ce commentaire');
        }
        
        $comment->delete();
        
        return redirect()->back()->with('success', 'Commentaire supprimé avec succès!');
    }
    
    /**
     * Approve a comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        // Only admin and editor can approve comments
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor') {
            return redirect()->back()
                           ->with('error', 'Vous n\'avez pas la permission d\'approuver des commentaires');
        }
        
        $comment = Comment::findOrFail($id);
        $comment->approved = true;
        $comment->save();
        
        return redirect()->back()->with('success', 'Commentaire approuvé avec succès!');
    }
    
    /**
     * Disapprove a comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disapprove($id)
    {
        // Only admin and editor can disapprove comments
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'editor') {
            return redirect()->back()
                           ->with('error', 'Vous n\'avez pas la permission de désapprouver des commentaires');
        }
        
        $comment = Comment::findOrFail($id);
        $comment->approved = false;
        $comment->save();
        
        return redirect()->back()->with('success', 'Commentaire désapprouvé avec succès!');
    }
}
