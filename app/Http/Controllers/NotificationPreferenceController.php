<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Affiche le formulaire de gestion des préférences
    public function edit()
    {
        $user = Auth::user();
        $categories = Category::all();
        $preference = $user->notificationPreference;
        return view('notifications.preferences', compact('user', 'categories', 'preference'));
    }

    // Met à jour les préférences
    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'types' => 'nullable|array',
            'types.*' => 'in:event,comment,like',
        ]);
        $preference = $user->notificationPreference ?: new NotificationPreference(['user_id' => $user->id]);
        $preference->categories = $data['categories'] ?? [];
        $preference->types = $data['types'] ?? [];
        $preference->user_id = $user->id;
        $preference->save();
        return redirect()->route('notifications.preferences.edit')->with('success', 'Préférences mises à jour.');
    }
} 