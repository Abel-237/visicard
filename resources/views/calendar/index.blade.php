@extends('layouts.app')

@section('content')
<div class="calendar-bg d-flex align-items-start min-vh-100 py-5">
<div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="fw-bold display-5 mb-2" style="letter-spacing:1px;">Calendrier des événements</h1>
        <div class="d-flex gap-2">
            <div class="dropdown d-none d-md-inline-block me-2">
                    <button class="btn btn-outline-secondary dropdown-toggle rounded-pill" type="button" id="calendarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar me-1"></i> {{ ucfirst($calendar['monthName']) }} {{ $calendar['year'] }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="calendarDropdown">
                    @php
                        $currentYear = $calendar['year'];
                        $months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
                    @endphp
                    @for ($y = $currentYear - 1; $y <= $currentYear + 1; $y++)
                        <li><h6 class="dropdown-header">{{ $y }}</h6></li>
                        @foreach ($months as $index => $month)
                            <li>
                                <a class="dropdown-item {{ ($y == $calendar['year'] && $index + 1 == $calendar['month']) ? 'active' : '' }}" 
                                   href="{{ route('calendar.index', ['month' => $index + 1, 'year' => $y]) }}">
                                    {{ ucfirst($month) }}
                                </a>
                            </li>
                        @endforeach
                        @if ($y < $currentYear + 1)
                            <li><hr class="dropdown-divider"></li>
                        @endif
                    @endfor
                </ul>
            </div>
                <a href="{{ route('calendar.download-all') }}" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-file-download me-1"></i> <span class="d-none d-md-inline">Exporter</span> (iCal)
            </a>
        </div>
    </div>
    <!-- Navigation des mois -->
        <div class="calendar-card card mb-4 shadow-lg border-0 rounded-4">
            <div class="card-body bg-white rounded-4">
            <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('calendar.index', ['month' => $calendar['previousMonth'], 'year' => $calendar['previousYear']]) }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-chevron-left"></i> <span class="d-none d-md-inline">Mois précédent</span>
                </a>
                    <h2 class="fw-semibold h3 mb-0">{{ ucfirst($calendar['monthName']) }} {{ $calendar['year'] }}</h2>
                    <a href="{{ route('calendar.index', ['month' => $calendar['nextMonth'], 'year' => $calendar['nextYear']]) }}" class="btn btn-outline-secondary rounded-pill">
                    <span class="d-none d-md-inline">Mois suivant</span> <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- Filtres et légende -->
    <div class="row mb-4">
            <div class="col-12">
                <div class="calendar-card card shadow-lg border-0 rounded-4">
                    <div class="card-body py-2 bg-white rounded-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge rounded-pill bg-info text-white px-3 py-2">
                                    <i class="fas fa-circle me-1"></i> Aujourd'hui
                                </span>
                                    <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="fas fa-sync-alt me-1"></i> Aujourd'hui
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            @can('create', App\Models\Event::class)
                                <a href="{{ route('admin.events.create') }}" class="btn btn-sm btn-success rounded-pill">
                                <i class="fas fa-plus-circle"></i> Nouvel événement
                            </a>
                            @endcan
                            <div class="btn-group ms-2 d-none d-md-inline-flex">
                                    <button type="button" id="viewMonth" class="btn btn-sm btn-outline-secondary rounded-pill active">
                                    <i class="fas fa-th me-1"></i> Mois
                                </button>
                                    <button type="button" id="viewList" class="btn btn-sm btn-outline-secondary rounded-pill">
                                    <i class="fas fa-list me-1"></i> Liste
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Vue calendrier -->
        <div id="monthView" class="calendar-card card shadow-lg border-0 rounded-4">
            <div class="card-body p-0 bg-white rounded-4">
            <div class="table-responsive">
                    <table class="table table-bordered calendar-table mb-0 rounded-4 overflow-hidden">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Lun</th>
                            <th class="text-center">Mar</th>
                            <th class="text-center">Mer</th>
                            <th class="text-center">Jeu</th>
                            <th class="text-center">Ven</th>
                            <th class="text-center">Sam</th>
                            <th class="text-center">Dim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dayOfWeek = $calendar['firstDayOfWeek'];
                            $dayOfWeek = $dayOfWeek == 0 ? 7 : $dayOfWeek; // Adjust Sunday from 0 to 7
                            $day = 1;
                            $daysInMonth = $calendar['daysInMonth'];
                        @endphp
                        <tr>
                            @for ($i = 1; $i < $dayOfWeek; $i++)
                                <td class="calendar-day empty"></td>
                            @endfor
                            @while ($day <= $daysInMonth)
                                @if ($dayOfWeek > 7)
                                    </tr><tr>
                                    @php $dayOfWeek = 1; @endphp
                                @endif
                                    <td class="calendar-day {{ ($calendar['currentMonth'] && $day == $calendar['today']) ? 'today' : '' }} rounded-4">
                                        <div class="calendar-date fw-bold text-primary">{{ $day }}</div>
                                    @if (isset($eventsByDay[$day]))
                                        <div class="calendar-events">
                                            @foreach ($eventsByDay[$day] as $event)
                                                <div class="calendar-event" data-color="{{ $event->category->color ?? '#007bff' }}">
                                                    <a href="{{ route('events.show', $event->slug) }}" class="event-title">
                                                        {{ Str::limit($event->title, 20) }}
                                                    </a>
                                                    @if ($event->event_date)
                                                        <div class="event-time">
                                                            <i class="far fa-clock me-1"></i>{{ $event->event_date->format('H:i') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if (count($eventsByDay[$day]) > 3)
                                                <div class="more-events">
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#dayEventsModal{{ $day }}">
                                                        + {{ count($eventsByDay[$day]) - 3 }} autre(s)
                                                    </a>
                                                </div>
                                                <!-- Modal for events -->
                                                <div class="modal fade" id="dayEventsModal{{ $day }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title">
                                                                    <i class="far fa-calendar-alt me-2"></i>
                                                                    Événements du {{ $day }} {{ $calendar['monthName'] }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="list-group">
                                                                    @foreach ($eventsByDay[$day] as $event)
                                                                        <a href="{{ route('events.show', $event->slug) }}" class="list-group-item list-group-item-action">
                                                                            <div class="d-flex w-100 justify-content-between">
                                                                                <h6 class="mb-1">{{ $event->title }}</h6>
                                                                                @if ($event->event_date)
                                                                                    <small class="text-muted">
                                                                                        <i class="far fa-clock me-1"></i>{{ $event->event_date->format('H:i') }}
                                                                                    </small>
                                                                                @endif
                                                                            </div>
                                                                            <p class="mb-1 small">{{ Str::limit($event->excerpt ?? $event->content, 100) }}</p>
                                                                            <div class="d-flex align-items-center mt-2">
                                                                                <span class="badge" data-bg-color="{{ $event->category->color ?? '#007bff' }}">
                                                                                    {{ $event->category->name }}
                                                                                </span>
                                                                                @if ($event->location)
                                                                                    <span class="ms-2 small text-muted">
                                                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                @php
                                    $day++;
                                    $dayOfWeek++;
                                @endphp
                            @endwhile
                            @while ($dayOfWeek <= 7)
                                <td class="calendar-day empty"></td>
                                @php $dayOfWeek++; @endphp
                            @endwhile
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Vue liste (hidden by default) -->
        <div id="listView" class="calendar-card card shadow-lg border-0 rounded-4 d-none">
            <div class="card-body bg-white rounded-4">
                <h3 class="fw-semibold h4 mb-4">Événements de {{ ucfirst($calendar['monthName']) }} {{ $calendar['year'] }}</h3>
            @php
                $hasEvents = false;
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    if (isset($eventsByDay[$day])) {
                        $hasEvents = true;
                        break;
                    }
                }
            @endphp
            @if ($hasEvents)
                <div class="list-group">
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @if (isset($eventsByDay[$day]))
                            <div class="list-day-header mb-2">
                                <h4 class="h5">{{ $day }} {{ ucfirst($calendar['monthName']) }}</h4>
                            </div>
                            @foreach ($eventsByDay[$day] as $event)
                                <a href="{{ route('events.show', $event->slug) }}" class="list-group-item list-group-item-action mb-2 rounded event-item" 
                                   data-event-color="{{ $event->category->color ?? '#007bff' }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-1">{{ $event->title }}</h5>
                                        @if ($event->event_date)
                                            <span class="badge bg-light text-dark">
                                                <i class="far fa-clock me-1"></i>{{ $event->event_date->format('H:i') }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mb-1">{{ Str::limit($event->excerpt ?? $event->content, 150) }}</p>
                                    <div class="d-flex align-items-center mt-2">
                                        <span class="badge category-badge" data-category-color="{{ $event->category->color ?? '#007bff' }}">
                                            {{ $event->category->name }}
                                        </span>
                                        @if ($event->location)
                                            <span class="ms-2 small text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    @endfor
                </div>
            @else
                    <div class="alert alert-info rounded-4 shadow-sm border-0">
                    <i class="fas fa-info-circle me-2"></i> Aucun événement pour ce mois.
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .calendar-bg {
        background: linear-gradient(135deg, #f5f8ff 0%, #e6eaff 100%);
        min-height: 100vh;
    }
    .calendar-card {
        border-radius: 2rem !important;
        background: #fff;
        box-shadow: 0 8px 32px 0 rgba(36,45,224,0.10);
        margin-bottom: 2rem;
    }
    .calendar-table {
        table-layout: fixed;
        border-radius: 1.5rem !important;
        overflow: hidden;
    }
    .calendar-day {
        height: 150px;
        vertical-align: top;
        padding: 8px;
        transition: all 0.2s ease;
        border-radius: 1.2rem !important;
        background: #f8faff;
    }
    .calendar-day:hover {
        background-color: #e6f0ff;
        box-shadow: 0 2px 8px rgba(36,45,224,0.08);
    }
    .calendar-day.empty {
        background-color: #f9f9f9;
    }
    .calendar-day.today {
        background-color: #e6f7ff;
        box-shadow: 0 0 0 3px #6B73FF inset;
    }
    .calendar-date {
        font-weight: bold;
        margin-bottom: 8px;
        text-align: right;
        color: #6B73FF;
    }
    .calendar-events {
        overflow-y: auto;
        max-height: 110px;
    }
    .calendar-event {
        margin-bottom: 5px;
        padding: 5px 7px;
        border-radius: 8px;
        background-color: #fff;
        border-left: 4px solid #007bff;
        font-size: 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .calendar-event:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 3px 8px rgba(36,45,224,0.10);
        background-color: #f5f8ff;
    }
    .calendar-event a {
        text-decoration: none;
        color: #222;
        font-weight: 500;
    }
    .event-time {
        font-size: 10px;
        color: #666;
        margin-top: 2px;
    }
    .more-events {
        text-align: center;
        font-size: 11px;
        padding: 3px;
        background-color: #e9ecef;
        border-radius: 3px;
        margin-top: 5px;
    }
    .more-events a {
        color: #495057;
        text-decoration: none;
        font-weight: 500;
    }
    .more-events a:hover {
        text-decoration: underline;
    }
    .list-day-header {
        position: relative;
        padding-bottom: 5px;
    }
    .list-day-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 2px;
        background-color: #e9ecef;
    }
    .event-item {
        border-left: 4px solid #007bff;
        border-radius: 1rem !important;
        background: #f8faff;
        margin-bottom: 1rem;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .event-item:hover {
        box-shadow: 0 4px 16px rgba(36,45,224,0.10);
        transform: scale(1.01);
        background: #fff;
    }
    .badge.category-badge {
        border-radius: 8px;
        font-size: 0.95em;
        padding: 0.4em 1em;
    }
    @media (max-width: 768px) {
        .calendar-day {
            height: 120px;
            padding: 5px;
        }
        .calendar-events {
            max-height: 80px;
        }
        .calendar-event {
            padding: 3px 5px;
            margin-bottom: 3px;
        }
        .calendar-card { padding: 1.5rem !important; }
        .calendar-bg { padding: 0 !important; }
    }
</style>
@endpush

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Apply colors from data attributes
        document.querySelectorAll('.calendar-event[data-color]').forEach(function(el) {
            el.style.borderLeftColor = el.getAttribute('data-color');
        });
        
        document.querySelectorAll('.badge[data-bg-color]').forEach(function(el) {
            el.style.backgroundColor = el.getAttribute('data-bg-color');
        });
        
        // Apply colors to list view items
        document.querySelectorAll('.event-item[data-event-color]').forEach(function(el) {
            el.style.borderLeftColor = el.getAttribute('data-event-color');
        });
        
        document.querySelectorAll('.category-badge[data-category-color]').forEach(function(el) {
            el.style.backgroundColor = el.getAttribute('data-category-color');
        });
        
        // Toggle between month and list views
        const monthView = document.getElementById('monthView');
        const listView = document.getElementById('listView');
        const viewMonthBtn = document.getElementById('viewMonth');
        const viewListBtn = document.getElementById('viewList');
        
        if (viewMonthBtn && viewListBtn) {
            viewMonthBtn.addEventListener('click', function() {
                monthView.classList.remove('d-none');
                listView.classList.add('d-none');
                viewMonthBtn.classList.add('active');
                viewListBtn.classList.remove('active');
                localStorage.setItem('calendarViewPreference', 'month');
            });
            
            viewListBtn.addEventListener('click', function() {
                monthView.classList.add('d-none');
                listView.classList.remove('d-none');
                viewMonthBtn.classList.remove('active');
                viewListBtn.classList.add('active');
                localStorage.setItem('calendarViewPreference', 'list');
            });
            
            // Check for saved preference
            const savedView = localStorage.getItem('calendarViewPreference');
            if (savedView === 'list') {
                viewListBtn.click();
            }
        }
    });
</script>
@endsection 