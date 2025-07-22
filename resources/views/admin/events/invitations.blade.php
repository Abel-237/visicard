@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Gestion des invitations - {{ $event->title }}</h1>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire d'invitation -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Envoyer des invitations</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.invitations', $event->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="emails">Adresses email des participants</label>
                            <textarea class="form-control" id="emails" name="emails" rows="5" 
                                      placeholder="Entrez une adresse email par ligne"></textarea>
                            <small class="form-text text-muted">
                                Séparez les adresses email par des retours à la ligne.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="message">Message personnalisé (optionnel)</label>
                            <textarea class="form-control" id="message" name="message" rows="3" 
                                      placeholder="Ajoutez un message personnalisé à votre invitation"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Envoyer les invitations
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des invitations -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Invitations envoyées</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invitationsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Date d'envoi</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($event->invitations as $invitation)
                                <tr>
                                    <td>{{ $invitation->email }}</td>
                                    <td>{{ $invitation->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $invitation->status === 'accepted' ? 'success' : ($invitation->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ $invitation->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($invitation->status === 'pending')
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="resendInvitation({{ $invitation->id }})">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteInvitation({{ $invitation->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modèle d'invitation -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modèle d'invitation</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="template">Modèle d'email</label>
                        <textarea class="form-control" id="template" rows="5" readonly>
Cher(e) participant(e),

Vous êtes invité(e) à participer à l'événement "{{ $event->title }}" qui se tiendra le {{ $event->event_date->format('d/m/Y') }}.

Pour confirmer votre participation, veuillez cliquer sur le lien suivant :
[LIEN_D_INVITATION]

Nous espérons vous y voir nombreux !

Cordialement,
L'équipe d'organisation
                        </textarea>
                        <small class="form-text text-muted">
                            Ce modèle sera utilisé pour toutes les invitations. Les variables comme [LIEN_D_INVITATION] seront automatiquement remplacées.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette invitation ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#invitationsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            }
        });
    });

    function resendInvitation(invitationId) {
        // Implémenter la logique de renvoi d'invitation
        alert('Fonctionnalité à implémenter');
    }

    function deleteInvitation(invitationId) {
        $('#deleteModal').modal('show');
        $('#confirmDelete').off('click').on('click', function() {
            // Implémenter la logique de suppression
            $('#deleteModal').modal('hide');
            alert('Fonctionnalité à implémenter');
        });
    }
</script>
@endpush 