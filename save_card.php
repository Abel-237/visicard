<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'business_cards';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}

// Traitement des données POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $countryCode = $_POST['countryCode'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $fullPhone = $countryCode . ' ' . $phone; // Combiner le code pays et le numéro
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $address = $_POST['address'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO cards (name, title, company, phone, email, website, address, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$name, $title, $company, $fullPhone, $email, $website, $address]);
        
        // Récupérer le nombre total de cartes
        $countStmt = $pdo->query("SELECT COUNT(*) FROM cards");
        $totalCards = $countStmt->fetchColumn();
        
        echo json_encode(['success' => true, 'totalCards' => $totalCards]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?> 