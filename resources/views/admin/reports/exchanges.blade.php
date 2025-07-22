@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main content -->
        <main role="main" class="col-md-10 ml-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Rapport des échanges</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportData('exchanges')">
                            <i class="fas fa-download"></i> Exporter
                        </button>
                    </div>
                    <div class="input-group">
                        <input type="date" class="form-control form-control-sm" id="startDate">
                        <input type="date" class="form-control form-control-sm" id="endDate">
                    </div>
                </div>
            </div>

            <!-- Statistiques générales -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total des échanges</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exchangeStats['total'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Échanges aujourd'hui</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exchangeStats['today'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Échanges cette semaine</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exchangeStats['this_week'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Échanges ce mois</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $exchangeStats['this_month'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Évolution des échanges -->
                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Évolution des échanges</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="exchangesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Échanges par méthode -->
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Échanges par méthode</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="methodsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top événements par échanges -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top événements par échanges</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Événement</th>
                                    <th>Nombre d'échanges</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exchangesByEvent as $event)
                                    <tr>
                                        <td>{{ $event->event->title }}</td>
                                        <td>{{ $event->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Évolution des échanges
    const exchangesData = @json($exchangesByDay);
    const labels = exchangesData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    });
    const data = exchangesData.map(item => item.total);

    const exchangesCtx = document.getElementById('exchangesChart').getContext('2d');
    new Chart(exchangesCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Nombre d\'échanges',
                data: data,
                fill: false,
                borderColor: '#3498db',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Échanges par méthode
    const methodsData = @json($exchangesByMethod);
    const methodLabels = methodsData.map(item => {
        switch(item.exchange_method) {
            case 'digital': return 'Numérique';
            case 'qr_code': return 'QR Code';
            case 'nfc': return 'NFC';
            default: return item.exchange_method;
        }
    });
    const methodData = methodsData.map(item => item.total);

    const methodsCtx = document.getElementById('methodsChart').getContext('2d');
    new Chart(methodsCtx, {
        type: 'doughnut',
        data: {
            labels: methodLabels,
            datasets: [{
                data: methodData,
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function exportData(type) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        alert('Veuillez sélectionner une période');
        return;
    }

    window.location.href = `{{ route('admin.reports.export') }}?type=${type}&start_date=${startDate}&end_date=${endDate}`;
}
</script>
@endsection 