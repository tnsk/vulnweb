<?php
/**
 * API Mass Assignment — HIGH
 * KASITLI BOZUK SAVUNMA: allowlist var (name,bio,role) ve role='admin' DENYLIST'lenir.
 * Bypass:  role=superadmin  (admin değil → geçer, ama yine de role≠user = yükseltme).
 */
require_once __DIR__ . '/_shared.php';

function api_profile_update(array $post): array
{
    api_profile_init();
    ids_log('api', json_encode($post));
    $allowed = ['name', 'bio', 'role'];
    foreach ($post as $k => $v) {
        if (!in_array($k, $allowed, true)) {
            continue;
        }
        if ($k === 'role' && $v === 'admin') {
            continue;                 // BOZUK: yalnızca 'admin' değeri bloklanır (denylist)
        }
        $_SESSION['api_profile'][$k] = $v;
    }
    return api_profile_result();
}

if (!defined('API_MODE')) {
    api_lab_ui('İpucu: role=admin bloklu — <code>role=superadmin</code> dene.');
}
