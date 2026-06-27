<?php
/**
 * API Mass Assignment — IMPOSSIBLE (güvenli referans)
 * - Katı ALLOWLIST: yalnızca güvenli, kullanıcıya ait alanlar (name, bio) bağlanabilir.
 * - role/is_admin gibi yetki alanları İSTEMCİDEN asla bağlanmaz (sunucu kontrolünde).
 */
require_once __DIR__ . '/_shared.php';

function api_profile_update(array $post): array
{
    api_profile_init();
    $allowed = ['name', 'bio'];      // yetki alanları yok
    foreach ($allowed as $field) {
        if (array_key_exists($field, $post)) {
            $_SESSION['api_profile'][$field] = (string) $post[$field];
        }
    }
    return api_profile_result();
}

if (!defined('API_MODE')) {
    api_lab_ui('Güvenli: yalnızca name/bio bağlanır; role/is_admin istemciden asla alınmaz.');
}
