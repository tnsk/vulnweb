<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();

$lvl = security_level();
if (!in_array($lvl, SecurityLevel::LEVELS, true)) {
    $lvl = 'low';
}

// JSON API yolu: ?api=profile (POST) -> mass-assignment endpoint
if (isset($_GET['api'])) {
    define('API_MODE', true);
    require __DIR__ . '/source/' . $lvl . '.php';
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(api_profile_update($_POST), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// HTML lab arayüzü
vuln_dispatch('api');
