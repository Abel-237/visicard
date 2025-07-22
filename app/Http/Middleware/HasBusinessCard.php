<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasBusinessCard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->businessCard) {
            return $next($request);
        }
        
        return redirect()->route('business-cards.create')
            ->with('warning', 'Vous devez d\'abord créer votre carte de visite.');
    }
} 