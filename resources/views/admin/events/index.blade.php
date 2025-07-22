@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestion des Événements</h4>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel Événement
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>
                                            @if($event->image)
                                                <img src="{{ Storage::url($event->image) }}" 
                                                     alt="{{ $event->title }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="text-center" style="width: 50px; height: 50px; background-color: #f8f9fa;">
                                                    <i class="fas fa-calendar fa-lg" style="line-height: 50px;"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $event->title }}
                                            @if($event->featured)
                                                <span class="badge bg-warning">À la une</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $event->category->color ?? '#6c757d' }}">
                                                <i class="fas {{ $event->category->icon ?? 'fa-folder' }}"></i>
                                                {{ $event->category->name }}
                                            </span>
                                        </td>
                                        <td>{{ $event->event_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : 'secondary' }}">
                                                {{ $event->status === 'published' ? 'Publié' : 'Brouillon' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('events.show', $event->slug) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.events.edit', $event) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.events.destroy', $event) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun événement trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 