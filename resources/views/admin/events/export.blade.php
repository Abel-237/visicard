<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport - {{ $event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #7f8c8d;
            margin: 0;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            color: #2c3e50;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $event->title }}</h1>
        <p>Rapport généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Statistiques générales</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $event->participants->count() }}</div>
                <div class="stat-label">Participants</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $event->businessCardExchanges->count() }}</div>
                <div class="stat-label">Échanges de cartes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $event->participants->unique('company')->count() }}</div>
                <div class="stat-label">Entreprises représentées</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $event->event_date->format('d/m/Y') }}</div>
                <div class="stat-label">Date de l'événement</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Liste des participants</h2>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Entreprise</th>
                    <th>Poste</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->participants as $participant)
                    <tr>
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->businessCard->company ?? 'Non spécifié' }}</td>
                        <td>{{ $participant->businessCard->position ?? 'Non spécifié' }}</td>
                        <td>{{ $participant->email }}</td>
                        <td>{{ $participant->pivot->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Échanges de cartes</h2>
        <table>
            <thead>
                <tr>
                    <th>Expéditeur</th>
                    <th>Destinataire</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->businessCardExchanges as $exchange)
                    <tr>
                        <td>{{ $exchange->sender->name }}</td>
                        <td>{{ $exchange->receiver->name }}</td>
                        <td>{{ $exchange->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $exchange->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système de gestion d'événements.</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html> 