<?php
/**
 * API Mass Assignment — MEDIUM
 * KASITLI BOZUK SAVUNMA: 'role' anahtarı blocklist'lenir ama 'is_admin' unutulmuş.
 * Bypass:  POST is_admin=1
 */
require_once __DIR__ . '/_shared.php';

function api_profile_update(array $post): array
{
    api_profile_init();
    ids_log('api', json_encode($post));
    foreach ($post as $k => $v) {
        if ($k === 'role') {
            continue;                 // BOZUK: yalnızca 'role' bloklanır
        }
        $_SESSION['api_profile'][$k] = $v;
    }
    return api_profile_result();
}

if (!defined('API_MODE')) {
    api_lab_ui('İpucu: <code>role</code> bloklu ama <code>is_admin=1</code> hâlâ geçiyor.');
}
