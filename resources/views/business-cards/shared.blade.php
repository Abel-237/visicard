<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de visite - {{ $businessCard->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card-container {
            max-width: 400px;
            margin: 50px auto;
        }
        
        .business-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        
        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 15px;
            display: block;
            object-fit: cover;
            background: #f8f9fa;
        }
        
        .company-logo {
            max-height: 40px;
            max-width: 120px;
            margin: 10px auto;
            display: block;
        }
        
        .card-body {
            padding: 30px 20px;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }
        
        .contact-item:hover {
            background-color: #f8f9fa;
        }
        
        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 16px;
        }
        
        .contact-info {
            flex: 1;
        }
        
        .contact-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .contact-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        
        .social-link:hover {
            transform: scale(1.1);
            color: white;
        }
        
        .linkedin { background: #0077b5; }
        .twitter { background: #1da1f2; }
        .facebook { background: #1877f2; }
        .instagram { background: #e4405f; }
        .website { background: #28a745; }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            color: white;
            font-size: 14px;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        .qr-section {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 15px;
        }
        
        .qr-code {
            max-width: 150px;
            margin: 10px auto;
        }
        
        @media (max-width: 480px) {
            .card-container {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .business-card {
                border-radius: 15px;
            }
            
            .card-header {
                padding: 20px 15px;
            }
            
            .card-body {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card-container">
            <div class="business-card">
                <div class="card-header">
                    @if($businessCard->logo)
                        <img src="{{ asset('storage/' . $businessCard->logo) }}" alt="Logo" class="company-logo">
                    @endif
                    <h2 class="mb-2">{{ $businessCard->name }}</h2>
                    @if($businessCard->position)
                        <p class="mb-1">{{ $businessCard->position }}</p>
                    @endif
                    @if($businessCard->company)
                        <p class="mb-0 opacity-75">{{ $businessCard->company }}</p>
                    @endif
                </div>
                
                <div class="card-body">
                    @if($businessCard->email)
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #dc3545;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">Email</div>
                                <div class="contact-value">
                                    <a href="mailto:{{ $businessCard->email }}" class="text-decoration-none">{{ $businessCard->email }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($businessCard->phone)
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #28a745;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">Téléphone</div>
                                <div class="contact-value">
                                    <a href="tel:{{ $businessCard->phone }}" class="text-decoration-none">{{ $businessCard->phone }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($businessCard->website)
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #17a2b8;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">Site web</div>
                                <div class="contact-value">
                                    <a href="{{ $businessCard->website }}" target="_blank" class="text-decoration-none">{{ $businessCard->website }}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($businessCard->address)
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #6f42c1;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">Adresse</div>
                                <div class="contact-value">{{ $businessCard->address }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if($businessCard->bio)
                        <div class="contact-item">
                            <div class="contact-icon" style="background: #fd7e14;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="contact-info">
                                <div class="contact-label">À propos</div>
                                <div class="contact-value">{{ $businessCard->bio }}</div>
                            </div>
                        </div>
                    @endif
                    
                    @if($businessCard->social_media && is_array($businessCard->social_media))
                        <div class="social-links">
                            @if(isset($businessCard->social_media['linkedin']))
                                <a href="{{ $businessCard->social_media['linkedin'] }}" target="_blank" class="social-link linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                            
                            @if(isset($businessCard->social_media['twitter']))
                                <a href="{{ $businessCard->social_media['twitter'] }}" target="_blank" class="social-link twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            
                            @if(isset($businessCard->social_media['facebook']))
                                <a href="{{ $businessCard->social_media['facebook'] }}" target="_blank" class="social-link facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            
                            @if(isset($businessCard->social_media['instagram']))
                                <a href="{{ $businessCard->social_media['instagram'] }}" target="_blank" class="social-link instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            
                            @if($businessCard->website)
                                <a href="{{ $businessCard->website }}" target="_blank" class="social-link website">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    @if($share)
                        <div class="qr-section">
                            <h6>Partager cette carte</h6>
                            <div class="qr-code">
                                {!! QrCode::size(150)->generate(url()->current()) !!}
                            </div>
                            <small class="text-muted">Scannez pour partager</small>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="footer">
                <p>Partagé via <a href="{{ url('/') }}">Plateforme d'événements</a></p>
                <p><small>Lien sécurisé • {{ $businessCard->updated_at->format('d/m/Y') }}</small></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 