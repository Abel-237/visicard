<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté et est un admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Récupérer les statistiques
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Nombre total de cartes
    $totalCards = $pdo->query("SELECT COUNT(*) FROM cards")->fetchColumn();
    
    // Nombre de cartes par type d'utilisateur
    $cardsByType = $pdo->query("
        SELECT u.user_type, COUNT(c.id) as count 
        FROM cards c 
        JOIN users u ON c.user_id = u.id 
        GROUP BY u.user_type
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Dernières cartes créées
    $latestCards = $pdo->query("
        SELECT c.*, u.name as creator_name, u.user_type 
        FROM cards c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
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

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 24px;
            font-weight: 600;
            color: #3498db;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f9fa;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-admin {
            background-color: #e74c3c;
            color: white;
        }

        .badge-user {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tableau de bord - Administration</h1>
            <a href="../logout.php" style="color: #e74c3c; text-decoration: none;">Déconnexion</a>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <h3>Total des cartes</h3>
                <div class="number"><?php echo $totalCards; ?></div>
            </div>
            <?php foreach ($cardsByType as $stat): ?>
            <div class="stat-card">
                <h3>Cartes <?php echo $stat['user_type'] === 'admin' ? 'administrateurs' : 'utilisateurs'; ?></h3>
                <div class="number"><?php echo $stat['count']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="table-container">
            <h2 style="margin-bottom: 20px;">Dernières cartes créées</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Titre</th>
                        <th>Entreprise</th>
                        <th>Créé par</th>
                        <th>Type</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestCards as $card): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($card['name']); ?></td>
                        <td><?php echo htmlspecialchars($card['title']); ?></td>
                        <td><?php echo htmlspecialchars($card['company']); ?></td>
                        <td><?php echo htmlspecialchars($card['creator_name']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $card['user_type']; ?>">
                                <?php echo $card['user_type']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($card['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html> 