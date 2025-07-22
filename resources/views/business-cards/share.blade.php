@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-gradient-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-share-alt"></i> Partager ma carte de visite
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Informations de la carte -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5>Votre carte de visite</h5>
                            <div class="card-info p-3 bg-light rounded">
                                <h6>{{ $businessCard->name }}</h6>
                                <p class="text-muted mb-1">{{ $businessCard->position }} @ {{ $businessCard->company }}</p>
                                <p class="text-muted mb-0">{{ $businessCard->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            @if($businessCard->logo)
                                <img src="{{ asset('storage/' . $businessCard->logo) }}" alt="Logo" class="img-fluid rounded" style="max-height: 80px;">
                            @endif
                        </div>
                    </div>

                    <!-- Lien public -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Lien public de votre carte</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $publicUrl }}" id="publicUrl" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard('publicUrl')">
                                        <i class="fas fa-copy"></i> Copier
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Ce lien est accessible à tous et ne nécessite pas de connexion</small>
                        </div>
                    </div>

                    <!-- Options de partage -->
                    <div class="row">
                        <!-- QR Code -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-qrcode"></i> QR Code</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="qr-code-container mb-3">
                                        {!! $qrCode !!}
                                    </div>
                                    <button class="btn btn-success btn-sm" onclick="downloadQRCode()">
                                        <i class="fas fa-download"></i> Télécharger
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-envelope"></i> Partager par Email</h6>
                                </div>
                                <div class="card-body">
                                    <form id="emailShareForm">
                                        <div class="form-group">
                                            <label>Email du destinataire *</label>
                                            <input type="email" class="form-control" name="recipient_email" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Nom du destinataire</label>
                                            <input type="text" class="form-control" name="recipient_name">
                                        </div>
                                        <div class="form-group">
                                            <label>Message personnalisé</label>
                                            <textarea class="form-control" name="custom_message" rows="3" placeholder="Message optionnel à inclure dans l'email..."></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Sujet de l'email</label>
                                            <input type="text" class="form-control" name="subject" placeholder="Carte de visite virtuelle">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-paper-plane"></i> Envoyer par Email
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fab fa-whatsapp"></i> Partager via WhatsApp</h6>
                                </div>
                                <div class="card-body">
                                    <form id="whatsappShareForm">
                                        <div class="form-group">
                                            <label>Message personnalisé</label>
                                            <textarea class="form-control" name="custom_message" rows="3" placeholder="Message à inclure avec le lien..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fab fa-whatsapp"></i> Ouvrir WhatsApp
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- NFC (Optionnel) -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-wifi"></i> NFC (Bêta)</h6>
                                </div>
                                <div class="card-body text-center">
                                    <p class="text-muted">Fonctionnalité en développement</p>
                                    <button class="btn btn-warning btn-block" onclick="shareViaNFC()" disabled>
                                        <i class="fas fa-wifi"></i> Activer NFC
                                    </button>
                                    <small class="text-muted">Nécessite un appareil compatible NFC</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Statistiques de partage</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center" id="sharingStats">
                                        <div class="col-md-3">
                                            <h4 class="text-primary" id="totalShares">0</h4>
                                            <small>Total partages</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-success" id="totalViews">0</h4>
                                            <small>Vues totales</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-info" id="emailShares">0</h4>
                                            <small>Partages email</small>
                                        </div>
                                        <div class="col-md-3">
                                            <h4 class="text-warning" id="qrShares">0</h4>
                                            <small>Partages QR</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast pour les notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="shareToast" class="toast" role="alert">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSharingStats();
    
    // Email sharing
    document.getElementById('emailShareForm').addEventListener('submit', function(e) {
        e.preventDefault();
        shareViaEmail();
    });
    
    // WhatsApp sharing
    document.getElementById('whatsappShareForm').addEventListener('submit', function(e) {
        e.preventDefault();
        shareViaWhatsApp();
    });
});

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999);
    document.execCommand('copy');
    showToast('Lien copié dans le presse-papiers !', 'success');
}

function shareViaEmail() {
    const form = document.getElementById('emailShareForm');
    const formData = new FormData(form);
    
    fetch(`/business-cards/{{ $businessCard->id }}/share/email`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            form.reset();
            loadSharingStats();
        } else {
            showToast('Erreur lors de l\'envoi', 'error');
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
    });
}

function shareViaWhatsApp() {
    const form = document.getElementById('whatsappShareForm');
    const formData = new FormData(form);
    
    fetch(`/business-cards/{{ $businessCard->id }}/share/whatsapp`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.open(data.whatsapp_url, '_blank');
            showToast('WhatsApp ouvert avec le message pré-rempli', 'success');
            form.reset();
            loadSharingStats();
        } else {
            showToast('Erreur lors de la génération du lien WhatsApp', 'error');
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
    });
}

function shareViaNFC() {
    fetch(`/business-cards/{{ $businessCard->id }}/share/nfc`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Données NFC générées', 'success');
        } else {
            showToast('Erreur NFC', 'error');
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
    });
}

function downloadQRCode() {
    fetch(`/business-cards/{{ $businessCard->id }}/share/qr`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const link = document.createElement('a');
            link.href = 'data:image/png;base64,' + data.qr_code;
            link.download = 'qr-code-carte.png';
            link.click();
            showToast('QR Code téléchargé', 'success');
        } else {
            showToast('Erreur lors de la génération du QR Code', 'error');
        }
    })
    .catch(error => {
        showToast('Erreur de connexion', 'error');
    });
}

function loadSharingStats() {
    fetch(`/business-cards/{{ $businessCard->id }}/share/stats`)
    .then(response => response.json())
    .then(data => {
        document.getElementById('totalShares').textContent = data.total_shares;
        document.getElementById('totalViews').textContent = data.total_views;
        document.getElementById('emailShares').textContent = data.email_shares;
        document.getElementById('qrShares').textContent = data.qr_shares;
    })
    .catch(error => {
        console.error('Erreur lors du chargement des statistiques:', error);
    });
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('shareToast');
    const toastMessage = document.getElementById('toastMessage');
    
    toastMessage.textContent = message;
    toast.classList.remove('bg-success', 'bg-danger', 'bg-info');
    
    switch(type) {
        case 'success':
            toast.classList.add('bg-success', 'text-white');
            break;
        case 'error':
            toast.classList.add('bg-danger', 'text-white');
            break;
        default:
            toast.classList.add('bg-info', 'text-white');
    }
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>
@endpush

@push('styles')
<style>
.card-header.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.qr-code-container {
    display: inline-block;
    padding: 10px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-info {
    border-left: 4px solid #667eea;
}

.toast-container {
    z-index: 1055;
}
</style>
@endpush 