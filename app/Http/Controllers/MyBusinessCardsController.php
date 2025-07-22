<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessCard;

class MyBusinessCardsController extends Controller
{
    public function index()
    {
        $businessCards = auth()->user()->businessCards()->latest()->get();
        return view('my-business-cards', compact('businessCards'));
    }
} 