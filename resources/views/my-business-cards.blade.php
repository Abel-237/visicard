<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Mes Cartes de Visite</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
        }

        .create-btn {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s;
        }

        .create-btn:hover {
            background-color: #2980b9;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .card-logo {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
        }

        .card-info h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 5px;
        }

        .card-info p {
            color: #666;
            font-size: 14px;
        }

        .card-details {
            margin-top: 15px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #555;
            font-size: 14px;
        }

        .detail-item i {
            color: #3498db;
            width: 20px;
        }

        .card-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .action-btn {
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
        }

        .edit-btn {
            background-color: #2ecc71;
            color: white;
        }

        .edit-btn:hover {
            background-color: #27ae60;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 48px;
            color: #95a5a6;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mes Cartes de Visite</h1>
            <a href="{{ route('business-card.create') }}" class="create-btn">
                <i class="fas fa-plus"></i>
                Créer une nouvelle carte
            </a>
        </div>

        @if($businessCards->isEmpty())
            <div class="empty-state">
                <i class="fas fa-id-card"></i>
                <h3>Aucune carte de visite</h3>
                <p>Vous n'avez pas encore créé de carte de visite.</p>
                <a href="{{ route('business-card.create') }}" class="create-btn">
                    <i class="fas fa-plus"></i>
                    Créer ma première carte
                </a>
            </div>
        @else
            <div class="cards-grid">
                @foreach($businessCards as $card)
                    <div class="card">
                        <div class="card-header">
                            @if($card->logo)
                                <img src="{{ asset('storage/' . $card->logo) }}" alt="Logo" class="card-logo">
                            @else
                                <div class="card-logo" style="background: #eee; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building" style="font-size: 24px; color: #95a5a6;"></i>
                                </div>
                            @endif
                            <div class="card-info">
                                <h2>{{ $card->name }}</h2>
                                <p>{{ $card->position }} chez {{ $card->company }}</p>
                            </div>
                        </div>

                        <div class="card-details">
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span>{{ $card->email }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span>{{ $card->phone }}</span>
                            </div>
                            @if($card->website)
                                <div class="detail-item">
                                    <i class="fas fa-globe"></i>
                                    <span>{{ $card->website }}</span>
                                </div>
                            @endif
                            @if($card->address)
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $card->address }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-actions">
                            <button class="action-btn" style="background-color: #9b59b6; color: white;" onclick="showQRCode('{{ $card->id }}', '{{ $card->name }}')" title="Voir QR Code">
                                <i class="fas fa-qrcode"></i>
                                QR Code
                            </button>
                            <a href="{{ route('business-card.edit', $card->id) }}" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i>
                                Modifier
                            </a>
                            <form action="{{ route('business-card.destroy', $card->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete-btn" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte ?')">
                                    <i class="fas fa-trash"></i>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal QR Code -->
    <div id="qrModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; text-align: center;">
            <h3 id="qrModalTitle">QR Code</h3>
            <div id="qrModalContent">
                <!-- Le QR code sera chargé ici -->
            </div>
            <p style="color: #666; margin: 10px 0;">
                <small>Scannez ce code QR pour accéder à la carte de visite</small>
            </p>
            <div style="margin-top: 15px;">
                <button onclick="downloadQRCode()" style="background: #3498db; color: white; border: none; padding: 8px 16px; border-radius: 5px; margin-right: 10px; cursor: pointer;">
                    <i class="fas fa-download"></i> Télécharger
                </button>
                <button onclick="closeQRModal()" style="background: #95a5a6; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">
                    Fermer
                </button>
            </div>
        </div>
    </div>

        <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
    function showQRCode(cardId, cardName) {
        // Générer le QR code directement avec JavaScript
        const qrUrl = `${window.location.origin}/business-cards/${cardId}`;
        const qrContainer = document.getElementById('qrModalContent');
        
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
            
            document.getElementById('qrModalTitle').textContent = `QR Code - ${cardName}`;
            
            // Stocker les données pour le téléchargement
            window.currentQRCode = {
                url: qrUrl,
                name: cardName
            };
            
            // Afficher le modal
            document.getElementById('qrModal').style.display = 'block';
        });
    }

    function closeQRModal() {
        document.getElementById('qrModal').style.display = 'none';
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

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('qrModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRModal();
        }
    });
    </script>
</body>
</html> 