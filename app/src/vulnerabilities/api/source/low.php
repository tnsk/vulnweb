<?php
/**
 * API Mass Assignment — LOW
 * KASITLI ZAFİYET: gönderilen TÜM alanlar profile bağlanır (allowlist yok).
 * Sömürü:  POST role=admin  (ya da is_admin=1) → yetki yükseltme.
 */
require_once __DIR__ . '/_shared.php';

function api_profile_update(array $post): array
{
    api_profile_init();
    ids_log('api', json_encode($post));
    // ----- ZAFİYETLİ: kör mass assignment -----
    foreach ($post as $k => $v) {
        $_SESSION['api_profile'][$k] = is_string($v) ? $v : $v;
    }
    // ------------------------------------------
    return api_profile_result();
}

if (!defined('API_MODE')) {
    api_lab_ui('İpucu: <code>role=admin</code> ya da <code>is_admin=1</code> alanını ekle.');
}
