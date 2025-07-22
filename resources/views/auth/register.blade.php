@extends('layouts.app')

@section('content')
dans la vue show register je veux que tu me fasses un design professionne
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="register-card shadow-lg border-0 rounded-4 bg-white p-4 p-md-5 mx-auto">
                    <div class="text-center mb-4">
                        <span class="register-icon d-inline-block mb-2"><i class="fas fa-user-plus fa-2x text-primary"></i></span>
                        <h2 class="fw-bold mb-1" style="letter-spacing:1px;">Créer un compte</h2>
                        <p class="text-muted mb-0">Rejoignez la plateforme et gérez vos événements facilement</p>
                    </div>
                    <form method="POST" action="{{ route('register') }}" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nom complet</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                dans la vue show register je veux que tu me fasses un design professionneg>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label fw-semibold">Confirmer le mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-user-plus me-2"></i> S'inscrire
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Déjà un compte ? <a href="{{ route('login') }}" class="text-primary fw-semibold">Se connecter</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
    .register-bg {
        background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
    }
    .register-card {
        border-radius: 2rem !important;
        background: #fff;
        box-shadow: 0 8px 32px 0 rgba(36,45,224,0.10);
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    .register-icon {
        background: #f5f8ff;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(36,45,224,0.08);
    }
    .input-group-text {
        border-radius: 8px 0 0 8px !important;
        border-right: 0 !important;
    }
    .form-control {
        border-radius: 0 8px 8px 0 !important;
        border-left: 0 !important;
        box-shadow: none !important;
    }
    .form-label {
        color: #222;
        font-weight: 600;
    }
    .btn-primary {
        background: #6B73FF;
        border-color: #6B73FF;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .btn-primary:hover {
        background: #000DFF;
        border-color: #000DFF;
    }
    .invalid-feedback {
        font-size: 0.95em;
    }
    @media (max-width: 767px) {
        .register-card { padding: 1.5rem !important; }
        .register-bg { padding: 0 !important; }
    }
</style>
@endpush
@endsection
