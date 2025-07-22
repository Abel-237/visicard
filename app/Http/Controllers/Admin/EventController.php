<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $events = Event::with(['category', 'user', 'tags'])
            ->latest()
            ->paginate(10);
            
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.events.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $event = new Event();
        $event->title = $validated['title'];
        $event->slug = Str::slug($validated['title']);
        $event->content = $validated['content'];
        $event->excerpt = $validated['excerpt'];
        $event->category_id = $validated['category_id'];
        $event->event_date = $validated['event_date'];
        $event->location = $validated['location'];
        $event->status = $validated['status'];
        $event->featured = $request->has('featured');
        $event->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
        }

        $event->save();

        if ($request->has('tags')) {
            $event->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement créé avec succès');
    }

    public function edit(Event $event)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.events.edit', compact('event', 'categories', 'tags'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
            'featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $event->title = $validated['title'];
        $event->slug = Str::slug($validated['title']);
        $event->content = $validated['content'];
        $event->excerpt = $validated['excerpt'];
        $event->category_id = $validated['category_id'];
        $event->event_date = $validated['event_date'];
        $event->location = $validated['location'];
        $event->status = $validated['status'];
        $event->featured = $request->has('featured');

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
        }

        $event->save();

        if ($request->has('tags')) {
            $event->tags()->sync($validated['tags']);
        } else {
            $event->tags()->detach();
        }

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement mis à jour avec succès');
    }

    public function destroy(Event $event)
    {
        // Supprimer l'image si elle existe
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Événement supprimé avec succès');
    }
} 