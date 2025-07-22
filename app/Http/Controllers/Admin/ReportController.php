<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BusinessCard;
use App\Models\Event;
use App\Models\BusinessCardExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function users()
    {
        // Statistiques des utilisateurs
        $userStats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'new_today' => User::whereDate('created_at', Carbon::today())->count(),
            'new_this_week' => User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Évolution des inscriptions
        $registrationsByMonth = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Top utilisateurs actifs
        $topActiveUsers = User::withCount(['businessCards', 'events'])
            ->orderBy('business_cards_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports.users', compact('userStats', 'registrationsByMonth', 'topActiveUsers'));
    }

    public function exchanges()
    {
        // Statistiques des échanges
        $exchangeStats = [
            'total' => BusinessCardExchange::count(),
            'today' => BusinessCardExchange::whereDate('created_at', Carbon::today())->count(),
            'this_week' => BusinessCardExchange::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'this_month' => BusinessCardExchange::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Échanges par méthode
        $exchangesByMethod = BusinessCardExchange::select('exchange_method', DB::raw('count(*) as total'))
            ->groupBy('exchange_method')
            ->get();

        // Échanges par événement
        $exchangesByEvent = BusinessCardExchange::select('event_id', DB::raw('count(*) as total'))
            ->with('event:id,title')
            ->groupBy('event_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        // Évolution des échanges
        $exchangesByDay = BusinessCardExchange::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('admin.reports.exchanges', compact(
            'exchangeStats',
            'exchangesByMethod',
            'exchangesByEvent',
            'exchangesByDay'
        ));
    }

    public function events()
    {
        // Statistiques des événements
        $eventStats = [
            'total' => Event::count(),
            'active' => Event::where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->count(),
            'upcoming' => Event::where('start_date', '>', now())->count(),
            'past' => Event::where('end_date', '<', now())->count(),
        ];

        // Événements par catégorie
        $eventsByCategory = Event::select('category_id', DB::raw('count(*) as total'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->get();

        // Top événements par participation
        $topEvents = Event::withCount('participants')
            ->orderBy('participants_count', 'desc')
            ->take(10)
            ->get();

        // Évolution des événements
        $eventsByMonth = Event::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', '>=', Carbon::now()->subYear()->year)
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        return view('admin.reports.events', compact(
            'eventStats',
            'eventsByCategory',
            'topEvents',
            'eventsByMonth'
        ));
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($type) {
            case 'users':
                $data = $this->exportUsers($startDate, $endDate);
                break;
            case 'exchanges':
                $data = $this->exportExchanges($startDate, $endDate);
                break;
            case 'events':
                $data = $this->exportEvents($startDate, $endDate);
                break;
            default:
                return response()->json(['error' => 'Type d\'export invalide'], 400);
        }

        $filename = "rapport_{$type}_" . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Écrire les en-têtes
            fputcsv($file, array_keys((array) $data->first()));
            
            // Écrire les données
            foreach ($data as $row) {
                fputcsv($file, (array) $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUsers($startDate, $endDate)
    {
        return User::whereBetween('created_at', [$startDate, $endDate])
            ->select([
                'id',
                'name',
                'email',
                'created_at',
                'is_active',
                'last_login_at'
            ])
            ->get();
    }

    private function exportExchanges($startDate, $endDate)
    {
        return BusinessCardExchange::whereBetween('created_at', [$startDate, $endDate])
            ->with(['sender:id,name,email', 'receiver:id,name,email', 'event:id,title'])
            ->get()
            ->map(function ($exchange) {
                return [
                    'id' => $exchange->id,
                    'sender' => $exchange->sender->name,
                    'sender_email' => $exchange->sender->email,
                    'receiver' => $exchange->receiver->name,
                    'receiver_email' => $exchange->receiver->email,
                    'event' => $exchange->event ? $exchange->event->title : 'N/A',
                    'method' => $exchange->exchange_method,
                    'created_at' => $exchange->created_at
                ];
            });
    }

    private function exportEvents($startDate, $endDate)
    {
        return Event::whereBetween('created_at', [$startDate, $endDate])
            ->with(['organizer:id,name,email', 'category:id,name'])
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'organizer' => $event->organizer->name,
                    'organizer_email' => $event->organizer->email,
                    'category' => $event->category ? $event->category->name : 'N/A',
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'participants_count' => $event->participants()->count(),
                    'created_at' => $event->created_at
                ];
            });
    }
} 