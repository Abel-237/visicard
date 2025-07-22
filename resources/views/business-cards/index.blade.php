@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">Annuaire des cartes de visite</h1>
                @auth
                    @if(!auth()->user()->businessCard)
                        <a href="{{ route('business-cards.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer ma carte de visite
                        </a>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Vous avez déjà une carte de visite. 
                            <a href="{{ route('business-cards.show', auth()->user()->businessCard) }}" class="alert-link">
                                Voir ma carte
                            </a>
                        </div>
                    @endif
                @else
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus me-2"></i>S'inscrire
                        </a>
                    </div>
                @endauth
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <form action="{{ route('business-cards.index') }}" method="GET" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, entreprise, poste..." value="{{ request('search') }}">
                        </div>
                        <div class="flex-grow-1">
                            <select name="industry" class="form-control">
                                <option value="">Tous les secteurs</option>
                                @foreach($industries as $industry)
                                    <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>
                                        {{ $industry }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </form>
                </div>
            </div>

            <!-- Liste des cartes de visite -->
            <div class="row">
                @forelse($businessCards as $card)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <div class="card-body">
                            <!-- <img src="{{ storage_path('app/public/' . $card->logo) }}" alt="" srcset=""> -->

                                <div class="text-center mb-4">
                                    {!! \App\Helpers\ImageHelper::displayProfileImage(
                                         $card->logo, 
                                        $card->name, 
                                        'mb-3', 
                                        ['style' => 'width: 150px; height: 150px; object-fit: cover;']
                                    ) !!}
                                    <h4 class="card-title mb-1">{{ $card->name }}</h4>
                                    <p class="text-muted mb-2">{{ $card->position }}</p>
                                    <span class="badge bg-primary">{{ $card->industry }}</span>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-center mb-3">{{ $card->company }}</h6>
                                </div>

                                <div class="contact-info">
                                    <p class="mb-2">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <a href="mailto:{{ $card->email }}" class="text-decoration-none">{{ $card->email }}</a>
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <a href="tel:{{ $card->phone }}" class="text-decoration-none">{{ $card->phone }}</a>
                                    </p>
                                    @if($card->website)
                                        <p class="mb-2">
                                            <i class="fas fa-globe text-primary me-2"></i>
                                            <a href="{{ $card->website }}" target="_blank" class="text-decoration-none">{{ $card->website }}</a>
                                        </p>
                                    @endif
                                </div>

                                @if($card->bio)
                                    <p class="card-text mt-3">{{ Str::limit($card->bio, 100) }}</p>
                                @endif

                                <div class="mt-4 text-center">
                                    <a href="{{ route('business-cards.show', $card) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> Voir le profil
                                    </a>
                                    @if(auth()->check() && auth()->id() === $card->user_id)
                                        <a href="{{ route('business-cards.edit', $card) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    @endif
                                    <button class="btn btn-outline-info btn-sm" onclick="showQRCode('{{ $card->id }}', '{{ $card->name }}')" title="Voir QR Code">
                                        <i class="fas fa-qrcode"></i> QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune carte de visite trouvée. Soyez le premier à créer la vôtre !
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="row mt-4">
                <div class="col-12">
                    {{ $businessCards->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <!-- Le QR code sera chargé ici -->
                </div>
                <p class="text-muted mt-2">
                    <small>Scannez ce code QR pour accéder à la carte de visite</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="downloadQRCode()">
                    <i class="fas fa-download me-1"></i>Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .contact-info a {
        color: inherit;
    }
    .contact-info a:hover {
        color: var(--bs-primary);
    }
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .card-body {
        padding: 2rem;
    }
    .img-thumbnail {
        border: 3px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 1em;
    }
</style>
@endpush

@push('scripts')
<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function showQRCode(cardId, cardName) {
    // Générer le QR code directement avec JavaScript
    const qrUrl = `${window.location.origin}/business-cards/${cardId}`;
    const qrContainer = document.getElementById('qrCodeContainer');
    
    // Vider le conteneur
    qrContainer.innerHTML = '';
    
    // Générer le QR code
    QRCode.toCanvas(qrContainer, qrUrl, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    }, function (error) {
        if (error) {
            console.error('Erreur lors de la génération du QR code:', error);
            qrContainer.innerHTML = '<p class="text-danger">Erreur lors de la génération du QR code</p>';
            return;
        }
        
        document.getElementById('qrCodeModalLabel').textContent = `QR Code - ${cardName}`;
        
        // Stocker les données pour le téléchargement
        window.currentQRCode = {
            url: qrUrl,
            name: cardName
        };
        
        // Afficher le modal
        new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
    });
}

function downloadQRCode() {
    if (window.currentQRCode) {
        QRCode.toDataURL(window.currentQRCode.url, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error, url) {
            if (error) {
                console.error('Erreur lors de la génération du QR code:', error);
                alert('Erreur lors de la génération du QR code');
                return;
            }
            
            const link = document.createElement('a');
            link.download = `qr-code-${window.currentQRCode.name}.png`;
            link.href = url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
}
</script>
@endpush
@endsection 