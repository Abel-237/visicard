@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Modération des commentaires</h1>
    <span class="badge bg-primary">{{ $comments->total() }} commentaires</span>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('admin.comments') }}" method="GET" class="row g-3">
            <div class="col-md-5">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Rechercher un commentaire..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-5">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tous les commentaires</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente d'approbation</option>
                    <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approuvés</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.comments') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Comments -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Contenu</th>
                        <th>Utilisateur</th>
                        <th>Événement</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            <td style="max-width: 300px;">
                                <div class="text-truncate">
                                    @if($comment->parent_id)
                                        <span class="badge bg-secondary me-1">Réponse</span> 
                                    @endif
                                    {{ $comment->content }}
                                </div>
                            </td>
                            <td>
                                {{ $comment->user->name }}
                            </td>
                            <td>
                                <a href="{{ route('events.show', $comment->event->slug) }}" target="_blank">
                                    {{ Str::limit($comment->event->title, 30) }}
                                </a>
                            </td>
                            <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($comment->approved)
                                    <span class="badge bg-success">Approuvé</span>
                                @else
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('events.show', $comment->event->slug) }}#comment-{{ $comment->id }}" 
                                       class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$comment->approved)
                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" data-bs-target="#deleteCommentModal{{ $comment->id }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteCommentModal{{ $comment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Êtes-vous sûr de vouloir supprimer ce commentaire ?</p>
                                                <div class="alert alert-secondary">
                                                    <strong>{{ $comment->user->name }} :</strong> {{ $comment->content }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <p class="text-muted mb-0">Aucun commentaire trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $comments->withQueryString()->links() }}
</div>
@endsection 