<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de visite virtuelle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .card-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📇 Carte de visite virtuelle</h1>
        <p>{{ $sender_name }} vous a partagé sa carte de visite</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $recipient_name }},</p>

        @if($custom_message)
            <p>{{ $custom_message }}</p>
        @else
            <p>J'espère que ce message vous trouve bien. Je vous partage ma carte de visite virtuelle pour faciliter notre échange de coordonnées.</p>
        @endif

        <div class="card-info">
            <h3>{{ $sender_name }}</h3>
            @if($position)
                <p><strong>Poste :</strong> {{ $position }}</p>
            @endif
            @if($company)
                <p><strong>Entreprise :</strong> {{ $company }}</p>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ $share_url }}" class="btn">Voir la carte complète</a>
        </div>

        <p style="margin-top: 30px;">
            <small>
                Cette carte de visite a été partagée via notre plateforme de gestion d'événements professionnels.
                Vous pouvez accéder à toutes les informations de contact et réseaux sociaux en cliquant sur le bouton ci-dessus.
            </small>
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} - Plateforme de gestion d'événements</p>
        <p>Ce lien est sécurisé et ne peut être modifié</p>
    </div>
</body>
</html> 