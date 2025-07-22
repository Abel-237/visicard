<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::withCount(['events' => function ($query) {
            $query->published();
        }])->get();
        
        return response()->json($categories);
    }
} 