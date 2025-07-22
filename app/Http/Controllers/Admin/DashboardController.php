<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Event;
use App\Models\User;
use App\Models\BusinessCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Statistics counts
        $stats = [
            'usersCount' => User::count(),
            'eventsCount' => Event::count(),
            'publishedEventsCount' => Event::published()->count(),
            'categoriesCount' => Category::count(),
            'commentsCount' => Comment::count(),
            'businessCardsCount' => BusinessCard::count(),
            'newUsersToday' => User::whereDate('created_at', Carbon::today())->count(),
            'newEventsToday' => Event::whereDate('created_at', Carbon::today())->count(),
            'newCommentsToday' => Comment::whereDate('created_at', Carbon::today())->count(),
            'newBusinessCardsToday' => BusinessCard::whereDate('created_at', Carbon::today())->count(),
            'uniqueCompaniesCount' => BusinessCard::select('company')->distinct()->count(),
        ];

        // Monthly events chart data
        $monthlyEvents = Event::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Most viewed events
        $popularEvents = Event::orderBy('views', 'desc')
            ->with('category', 'user')
            ->take(5)
            ->get();

        // Latest comments
        $latestComments = Comment::with(['user', 'event'])
            ->latest()
            ->take(5)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(5)
            ->get();

        // Latest business cards
        $latestBusinessCards = BusinessCard::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyEvents',
            'popularEvents',
            'latestComments',
            'upcomingEvents',
            'latestBusinessCards'
        ));
    }

    /**
     * Display users list.
     *
     * @return \Illuminate\Http\Response
     */
    public function users(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $usersQuery->where('role', $role);
        }

        $users = $usersQuery->latest()->paginate(15);

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    /**
     * Edit user form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,editor,user',
            'notification_preferences' => 'boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->notification_preferences = $request->has('notification_preferences');
        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    /**
     * Delete user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Display comments moderation page.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function comments(Request $request)
    {
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $commentsQuery = Comment::with(['user', 'event']);

        if ($status === 'pending') {
            $commentsQuery->where('approved', false);
        } elseif ($status === 'approved') {
            $commentsQuery->where('approved', true);
        }

        if ($search) {
            $commentsQuery->where('content', 'like', "%{$search}%")
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('event', function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                });
        }

        $comments = $commentsQuery->latest()->paginate(15);

        return view('admin.comments.index', compact('comments', 'status', 'search'));
    }

    /**
     * Approve comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->approved = true;
        $comment->save();

        return redirect()->back()
            ->with('success', 'Commentaire approuvé avec succès');
    }

    /**
     * Delete comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()
            ->with('success', 'Commentaire supprimé avec succès');
    }
}
