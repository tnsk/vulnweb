<?php
/** jwt_lib.php — minimal JWT (HS256) yardımcıları (eğitim amaçlı, kütüphane bağımlılığı yok). */

function b64url_encode(string $d): string
{
    return rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
}

function b64url_decode(string $d): string
{
    return (string) base64_decode(strtr($d, '-_', '+/'), false);
}

function jwt_make(array $payload, string $secret, string $alg = 'HS256'): string
{
    $h = b64url_encode(json_encode(['typ' => 'JWT', 'alg' => $alg]));
    $p = b64url_encode(json_encode($payload));
    $sig = $alg === 'none' ? '' : b64url_encode(hash_hmac('sha256', "$h.$p", $secret, true));
    return "$h.$p.$sig";
}

function jwt_header(string $jwt): array
{
    $p = explode('.', $jwt);
    return json_decode(b64url_decode($p[0] ?? ''), true) ?: [];
}

function jwt_payload(string $jwt): array
{
    $p = explode('.', $jwt);
    return json_decode(b64url_decode($p[1] ?? ''), true) ?: [];
}

function jwt_verify_hs256(string $jwt, string $secret): bool
{
    $p = explode('.', $jwt);
    if (count($p) !== 3) {
        return false;
    }
    $expected = b64url_encode(hash_hmac('sha256', $p[0] . '.' . $p[1], $secret, true));
    return hash_equals($expected, $p[2]);
}
