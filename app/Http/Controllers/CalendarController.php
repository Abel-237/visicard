<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as ICalEvent;

class CalendarController extends Controller
{
    /**
     * Display the calendar view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        // Get the first day of the month
        $firstDay = Carbon::createFromDate($year, $month, 1);
        
        // Get the last day of the month
        $lastDay = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Get the events for this month
        $events = Event::where('status', 'published')
            ->where(function($query) use ($firstDay, $lastDay) {
                $query->whereBetween('event_date', [$firstDay->startOfDay(), $lastDay->endOfDay()])
                    ->orWhereBetween('published_at', [$firstDay->startOfDay(), $lastDay->endOfDay()]);
            })
            ->with('category')
            ->get();
        
        // Organize events by day
        $eventsByDay = [];
        foreach ($events as $event) {
            $day = $event->event_date ? $event->event_date->day : $event->published_at->day;
            if (!isset($eventsByDay[$day])) {
                $eventsByDay[$day] = [];
            }
            $eventsByDay[$day][] = $event;
        }
        
        // Prepare the calendar data
        $calendar = [
            'month' => $month,
            'year' => $year,
            'monthName' => $firstDay->translatedFormat('F'),
            'daysInMonth' => $lastDay->day,
            'firstDayOfWeek' => $firstDay->dayOfWeek,
            'previousMonth' => $firstDay->copy()->subMonth()->month,
            'previousYear' => $firstDay->copy()->subMonth()->year,
            'nextMonth' => $firstDay->copy()->addMonth()->month,
            'nextYear' => $firstDay->copy()->addMonth()->year,
            'today' => Carbon::now()->day,
            'currentMonth' => Carbon::now()->month === (int)$month,
            'currentYear' => Carbon::now()->year === (int)$year,
        ];
        
        return view('calendar.index', compact('calendar', 'eventsByDay'));
    }
    
    /**
     * Generate and download an ICS file for an event.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadIcs($id)
    {
        $event = Event::with('user')->findOrFail($id);
        
        // Create the calendar
        $calendar = Calendar::create(config('app.name'))
            ->productIdentifier('-//Laravel//Event Calendar//EN');
        
        // Add the event
        $icalEvent = ICalEvent::create()
            ->name($event->title)
            ->description($event->excerpt ?: strip_tags($event->content));
        
        // Add organizer - use event creator's email if available, otherwise use default
        $organizerEmail = $event->user->email ?: (config('mail.from.address') ?: 'noreply@example.com');
        $organizerName = $event->user->name ?: (config('mail.from.name') ?: config('app.name'));
        $icalEvent->organizer($organizerEmail, $organizerName);
        
        // Add event date if available
        if ($event->event_date) {
            // If there's no end date, assume it's a 2-hour event
            $endDate = $event->event_end_date ?? $event->event_date->copy()->addHours(2);
            $icalEvent->startsAt($event->event_date)
                     ->endsAt($endDate);
        } else {
            // If no specific date, use published date
            $icalEvent->startsAt($event->published_at)
                     ->endsAt($event->published_at->copy()->addHours(1));
        }
        
        // Add location if available
        if ($event->location) {
            $icalEvent->address($event->location);
        }
        
        // Add the event to the calendar
        $calendar->event($icalEvent);
        
        // Return the response with the calendar data
        return response($calendar->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $event->slug . '.ics"');
    }
    
    /**
     * Generate and download an ICS file for all upcoming events.
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadAllIcs()
    {
        // Create the calendar
        $calendar = Calendar::create(config('app.name') . ' - Événements')
            ->productIdentifier('-//Laravel//Event Calendar//EN');
        
        // Get upcoming events
        $events = Event::with('user')->where('status', 'published')
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date')
            ->take(50) // Limit to 50 events
            ->get();
        
        foreach ($events as $event) {
            // Add the event
            $icalEvent = ICalEvent::create()
                ->name($event->title)
                ->description($event->excerpt ?: strip_tags($event->content));
            
            // Add organizer - use event creator's email if available, otherwise use default
            $organizerEmail = $event->user->email ?: (config('mail.from.address') ?: 'noreply@example.com');
            $organizerName = $event->user->name ?: (config('mail.from.name') ?: config('app.name'));
            $icalEvent->organizer($organizerEmail, $organizerName);
            
            // Add event date
            if ($event->event_date) {
                // If there's no end date, assume it's a 2-hour event
                $endDate = $event->event_end_date ?? $event->event_date->copy()->addHours(2);
                $icalEvent->startsAt($event->event_date)
                         ->endsAt($endDate);
            } else {
                // If no specific date, use published date
                $icalEvent->startsAt($event->published_at)
                         ->endsAt($event->published_at->copy()->addHours(1));
            }
            
            // Add location if available
            if ($event->location) {
                $icalEvent->address($event->location);
            }
            
            // Add the event to the calendar
            $calendar->event($icalEvent);
        }
        
        // Return the response with the calendar data
        return response($calendar->get())
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="calendrier-evenements.ics"');
    }
    
    /**
     * Set a reminder for an event.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setReminder(Request $request, $id)
    {
        $event = Event::with('user')->findOrFail($id);
        
        if (!$event->event_date) {
            return redirect()->back()->with('error', 'Impossible de définir un rappel pour un événement sans date.');
        }
        
        // Get the reminder time
        $reminderTime = $request->input('reminder_time', 'day');
        
        switch ($reminderTime) {
            case 'hour':
                $reminderDate = $event->event_date->copy()->subHour();
                $reminderText = "1 heure avant";
                break;
            case 'day':
                $reminderDate = $event->event_date->copy()->subDay();
                $reminderText = "1 jour avant";
                break;
            case 'week':
                $reminderDate = $event->event_date->copy()->subWeek();
                $reminderText = "1 semaine avant";
                break;
            default:
                $reminderDate = $event->event_date->copy()->subDay();
                $reminderText = "1 jour avant";
        }
        
        // Create the reminder (you could store this in the database or use a third-party service)
        // For this example, we'll just show a message and provide an ICS file
        
        return redirect()->back()->with('success', 'Rappel défini pour ' . $reminderText . ' l\'événement.');
    }
}
