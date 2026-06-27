<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();

$vuln_id = 'sqli';
$lvl = security_level();
if (!in_array($lvl, SecurityLevel::LEVELS, true)) {
    $lvl = 'low';
}
ob_start();
require __DIR__ . '/source/' . $lvl . '.php';
$body = ob_get_clean();

render_vuln_page($vuln_id, $body);
