<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::withCount('events')->get();
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Only admin can create categories
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')
                           ->with('error', 'Vous n\'avez pas la permission de créer une catégorie');
        }
        
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only admin can create categories
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')
                           ->with('error', 'Vous n\'avez pas la permission de créer une catégorie');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:categories,name',
            'description' => 'nullable',
            'color' => 'nullable|max:7',
        ]);
        
        // Create a slug from the name
        $slug = Str::slug($validatedData['name']);
        
        // Create the category
        $category = new Category();
        $category->name = $validatedData['name'];
        $category->slug = $slug;
        $category->description = $validatedData['description'] ?? null;
        $category->color = $validatedData['color'] ?? '#3490dc';
        $category->created_by = Auth::id();
        $category->save();
        
        return redirect()->route('categories.index')
                       ->with('success', 'Catégorie créée avec succès!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $events = $category->events()->published()->paginate(12);
        return view('categories.show', compact('category', 'events'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        // Only admin can edit categories
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')
                           ->with('error', 'Vous n\'avez pas la permission de modifier une catégorie');
        }
        
        $category = Category::where('slug', $slug)->firstOrFail();
        
        return view('categories.edit', compact('category'));
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
        // Only admin can update categories
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')
                           ->with('error', 'Vous n\'avez pas la permission de modifier une catégorie');
        }
        
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable',
            'color' => 'nullable|max:7',
        ]);
        
        // Update the category
        $category->name = $validatedData['name'];
        $category->description = $validatedData['description'] ?? null;
        $category->color = $validatedData['color'] ?? '#3490dc';
        
        // Update slug if name has changed
        if ($category->isDirty('name')) {
            $category->slug = Str::slug($validatedData['name']);
        }
        
        $category->save();
        
        return redirect()->route('categories.index')
                       ->with('success', 'Catégorie mise à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        // Only admin can delete categories
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('categories.index')
                           ->with('error', 'Vous n\'avez pas la permission de supprimer une catégorie');
        }
        
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Check if category has events
        $eventsCount = Event::where('category_id', $category->id)->count();
        
        if ($eventsCount > 0) {
            return redirect()->route('categories.index')
                           ->with('error', 'Impossible de supprimer une catégorie qui contient des événements');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
                       ->with('success', 'Catégorie supprimée avec succès!');
    }
}
