@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main content -->
        <main role="main" class="col-md-10 ml-sm-auto px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Paramètres de la plateforme</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group mr-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('settingsForm').submit()">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('admin.settings.clear-cache') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sync"></i> Vider le cache
                        </a>
                    </div>
                    <a href="{{ route('admin.settings.toggle-maintenance') }}" class="btn btn-sm btn-outline-{{ $settings['maintenance_mode'] ? 'danger' : 'success' }}">
                        <i class="fas fa-tools"></i> {{ $settings['maintenance_mode'] ? 'Désactiver' : 'Activer' }} le mode maintenance
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form id="settingsForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Informations générales -->
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="platform_name">Nom de la plateforme</label>
                                    <input type="text" class="form-control @error('platform_name') is-invalid @enderror" 
                                           id="platform_name" name="platform_name" 
                                           value="{{ old('platform_name', $settings['platform_name']) }}">
                                    @error('platform_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="platform_description">Description</label>
                                    <textarea class="form-control @error('platform_description') is-invalid @enderror" 
                                              id="platform_description" name="platform_description" rows="3">{{ old('platform_description', $settings['platform_description']) }}</textarea>
                                    @error('platform_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="contact_email">Email de contact</label>
                                    <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" name="contact_email" 
                                           value="{{ old('contact_email', $settings['contact_email']) }}">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="platform_logo">Logo de la plateforme</label>
                                    @if($settings['platform_logo'])
                                        <div class="mb-2">
                                            <img src="{{ $settings['platform_logo'] }}" alt="Logo actuel" class="img-thumbnail" style="max-height: 100px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('platform_logo') is-invalid @enderror" 
                                           id="platform_logo" name="platform_logo">
                                    @error('platform_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fonctionnalités -->
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Fonctionnalités</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_registration" 
                                               name="enable_registration" value="1" 
                                               {{ old('enable_registration', $settings['enable_registration']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_registration">Inscription des utilisateurs</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_business_cards" 
                                               name="enable_business_cards" value="1" 
                                               {{ old('enable_business_cards', $settings['enable_business_cards']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_business_cards">Cartes de visite</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_events" 
                                               name="enable_events" value="1" 
                                               {{ old('enable_events', $settings['enable_events']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_events">Événements</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_qr_codes" 
                                               name="enable_qr_codes" value="1" 
                                               {{ old('enable_qr_codes', $settings['enable_qr_codes']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_qr_codes">QR Codes</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="enable_nfc" 
                                               name="enable_nfc" value="1" 
                                               {{ old('enable_nfc', $settings['enable_nfc']) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="enable_nfc">NFC</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="max_business_cards">Nombre maximum de cartes de visite par utilisateur</label>
                                    <input type="number" class="form-control @error('max_business_cards') is-invalid @enderror" 
                                           id="max_business_cards" name="max_business_cards" 
                                           value="{{ old('max_business_cards', $settings['max_business_cards']) }}">
                                    @error('max_business_cards')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="max_events">Nombre maximum d'événements par utilisateur</label>
                                    <input type="number" class="form-control @error('max_events') is-invalid @enderror" 
                                           id="max_events" name="max_events" 
                                           value="{{ old('max_events', $settings['max_events']) }}">
                                    @error('max_events')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personnalisation -->
                    <div class="col-md-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Personnalisation</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="theme_color">Couleur principale</label>
                                    <input type="color" class="form-control @error('theme_color') is-invalid @enderror" 
                                           id="theme_color" name="theme_color" 
                                           value="{{ old('theme_color', $settings['theme_color']) }}">
                                    @error('theme_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="theme_secondary_color">Couleur secondaire</label>
                                    <input type="color" class="form-control @error('theme_secondary_color') is-invalid @enderror" 
                                           id="theme_secondary_color" name="theme_secondary_color" 
                                           value="{{ old('theme_secondary_color', $settings['theme_secondary_color']) }}">
                                    @error('theme_secondary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<style>
.custom-switch {
    padding-left: 2.25rem;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #3498db;
    border-color: #3498db;
}

.custom-control-input:focus ~ .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
}
</style>
@endsection 