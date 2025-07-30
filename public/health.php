<?php
// Fichier de healthcheck simple pour Railway
header('Content-Type: application/json');

try {
    // Vérifier que Laravel est accessible
    if (file_exists('../vendor/autoload.php')) {
        echo json_encode([
            'status' => 'healthy',
            'message' => 'Laravel application is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION
        ]);
    } else {
        echo json_encode([
            'status' => 'unhealthy',
            'message' => 'Laravel vendor directory not found',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?> 