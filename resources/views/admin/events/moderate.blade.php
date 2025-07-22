@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Modération - {{ $event->title }}</h1>
        <div>
            <a href="{{ route('admin.events.dashboard', $event->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.events.moderate', $event->id) }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select class="form-select" name="status">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            En attente
                        </option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                            Approuvé
                        </option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                            Rejeté
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Entreprise</label>
                    <input type="text" 
                           class="form-control" 
                           name="company" 
                           value="{{ request('company') }}"
                           placeholder="Filtrer par entreprise">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Date d'inscription</label>
                    <input type="date" 
                           class="form-control" 
                           name="registration_date" 
                           value="{{ request('registration_date') }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.events.moderate', $event->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des participants -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Participant</th>
                            <th>Entreprise</th>
                            <th>Poste</th>
                            <th>Date d'inscription</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participants as $participant)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($participant->profile_photo_path)
                                            <img src="{{ Storage::url($participant->profile_photo_path) }}" 
                                                 alt="{{ $participant->name }}"
                                                 class="rounded-circle me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary me-2 d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $participant->name }}</div>
                                            <small class="text-muted">{{ $participant->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $participant->businessCard->company ?? 'Non spécifié' }}</td>
                                <td>{{ $participant->businessCard->position ?? 'Non spécifié' }}</td>
                                <td>{{ $participant->pivot->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $participant->pivot->status === 'approved' ? 'success' : 
                                                           ($participant->pivot->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $participant->pivot->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewModal{{ $participant->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($participant->pivot->status === 'pending')
                                            <form action="{{ route('admin.events.moderate', $event->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $participant->id }}">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.events.moderate', $event->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $participant->id }}">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Modal de détails -->
                                    <div class="modal fade" id="viewModal{{ $participant->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Détails du participant</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-4">
                                                        @if($participant->profile_photo_path)
                                                            <img src="{{ Storage::url($participant->profile_photo_path) }}" 
                                                                 alt="{{ $participant->name }}"
                                                                 class="rounded-circle mb-3"
                                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                                        @else
                                                            <div class="rounded-circle bg-secondary mb-3 d-flex align-items-center justify-content-center mx-auto"
                                                                 style="width: 100px; height: 100px;">
                                                                <i class="fas fa-user fa-3x text-white"></i>
                                                            </div>
                                                        @endif
                                                        <h5>{{ $participant->name }}</h5>
                                                        <p class="text-muted">{{ $participant->email }}</p>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Entreprise:</strong><br>
                                                            {{ $participant->businessCard->company ?? 'Non spécifié' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Poste:</strong><br>
                                                            {{ $participant->businessCard->position ?? 'Non spécifié' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Téléphone:</strong><br>
                                                            {{ $participant->businessCard->phone ?? 'Non spécifié' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Site web:</strong><br>
                                                            {{ $participant->businessCard->website ?? 'Non spécifié' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <p><strong>Bio:</strong><br>
                                                        {{ $participant->businessCard->bio ?? 'Non spécifié' }}</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Fermer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Aucun participant trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $participants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
}

.modal-body img {
    border: 3px solid #fff;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endpush 