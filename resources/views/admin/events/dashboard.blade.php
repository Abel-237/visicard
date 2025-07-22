@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Tableau de bord des événements</h1>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des événements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                Événements actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_events'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Total des participants</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_participants'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                Cartes échangées</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_cards_exchanged'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Cartes de Visite -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Cartes de Visite</h6>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCardModal">
                        <i class="fas fa-plus"></i> Nouvelle carte
                    </button>
                </div>
                <div class="card-body bg-dark text-white">
                    <div class="row">
                        @foreach($businessCards as $card)
                        <div class="col-md-4 mb-4">
                            <div class="card bg-dark border-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($card->logo)
                                            <img src="{{ asset('storage/' . $card->logo) }}" alt="Logo" class="rounded-circle mr-3" style="width: 50px; height: 50px;">
                                        @else
                                            <div class="rounded-circle bg-primary mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="card-title text-white mb-0">{{ $card->name }}</h5>
                                            <p class="card-text text-muted mb-0">{{ $card->position }}</p>
                                        </div>
                                    </div>
                                    <div class="card-text">
                                        <p class="mb-1"><i class="fas fa-building mr-2"></i>{{ $card->company }}</p>
                                        <p class="mb-1"><i class="fas fa-envelope mr-2"></i>{{ $card->email }}</p>
                                        <p class="mb-1"><i class="fas fa-phone mr-2"></i>{{ $card->phone }}</p>
                                        @if($card->website)
                                            <p class="mb-1"><i class="fas fa-globe mr-2"></i>{{ $card->website }}</p>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <button class="btn btn-outline-light btn-sm mr-2" onclick="editCard({{ $card->id }})">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteCard({{ $card->id }})">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des événements -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des événements</h6>
            <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouvel événement
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="eventsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Participants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->category->name }}</td>
                            <td>{{ $event->event_date->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge badge-{{ $event->status === 'published' ? 'success' : 'warning' }}">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td>{{ $event->participants_count ?? 0 }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.events.stats', $event->id) }}" 
                                       class="btn btn-info btn-sm" 
                                       title="Statistiques">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('admin.events.qr-codes', $event->id) }}" 
                                       class="btn btn-success btn-sm" 
                                       title="QR Codes">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                    <a href="{{ route('events.edit', $event->id) }}" 
                                       class="btn btn-primary btn-sm" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.events.export', $event->id) }}" 
                                       class="btn btn-secondary btn-sm" 
                                       title="Exporter">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $events->links() }}
        </div>
    </div>
</div>

<!-- Modal pour ajouter une carte de visite -->
<div class="modal fade" id="addCardModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle carte de visite</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addCardForm">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" class="form-control bg-dark text-white" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Entreprise</label>
                        <input type="text" class="form-control bg-dark text-white" name="company" required>
                    </div>
                    <div class="form-group">
                        <label>Poste</label>
                        <input type="text" class="form-control bg-dark text-white" name="position" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control bg-dark text-white" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" class="form-control bg-dark text-white" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Site web</label>
                        <input type="url" class="form-control bg-dark text-white" name="website">
                    </div>
                    <div class="form-group">
                        <label>Logo</label>
                        <input type="file" class="form-control-file" name="logo">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveCard()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-dark {
        background-color: #1a1a1a !important;
    }
    .card.bg-dark {
        border: 1px solid #333;
    }
    .card.bg-dark .card-text {
        color: #ccc;
    }
    .modal-content.bg-dark {
        border: 1px solid #333;
    }
    .modal-content.bg-dark .form-control {
        border-color: #333;
    }
    .modal-content.bg-dark .form-control:focus {
        background-color: #2a2a2a;
        border-color: #444;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#eventsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });

    function editCard(cardId) {
        // Implémenter la logique de modification
        alert('Fonctionnalité à implémenter');
    }

    function deleteCard(cardId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette carte de visite ?')) {
            // Implémenter la logique de suppression
            alert('Fonctionnalité à implémenter');
        }
    }

    function saveCard() {
        // Implémenter la logique de sauvegarde
        alert('Fonctionnalité à implémenter');
    }
</script>
@endpush 