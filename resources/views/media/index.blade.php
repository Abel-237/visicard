@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-photo-video me-2"></i> Bibliothèque des médias</h2>
        <a href="{{ route('media.upload') }}" class="btn btn-primary">
            <i class="fas fa-upload"></i> Ajouter un média
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($media as $item)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    @if(Str::startsWith($item->file_type, 'image'))
                        <img src="{{ asset('storage/' . $item->file_path) }}" class="card-img-top" alt="{{ $item->original_name }}" style="height:180px;object-fit:cover;">
                    @elseif(Str::startsWith($item->file_type, 'video'))
                        <video controls style="width:100%;height:180px;object-fit:cover;">
                            <source src="{{ asset('storage/' . $item->file_path) }}" type="{{ $item->file_type }}">
                            Votre navigateur ne supporte pas la vidéo.
                        </video>
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px;">
                            <i class="fas fa-file fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body p-2">
                        <div class="small text-muted mb-1">{{ $item->original_name }}</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <form action="{{ route('media.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Supprimer ce média ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            <span class="badge bg-secondary">{{ strtoupper(Str::before($item->file_type, '/')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Aucun média pour le moment.</div>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $media->links() }}
    </div>
</div>
@endsection 