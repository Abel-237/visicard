<?php
// Fichier de healthcheck ultra-simple pour Railway
header('Content-Type: application/json');

echo json_encode([
    'status' => 'healthy',
    'message' => 'Application is running',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION
]);
?> 