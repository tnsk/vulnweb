<?php
/**
 * functions.php — global yardımcılar. Bunlar uygulama "chrome"u içindir;
 * zafiyetli lab kodu KASITLI olarak bunları kullanmaz.
 */
declare(strict_types=1);

/** Güvenli HTML çıktısı (framework arayüzü için — lab içeriği için DEĞİL). */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Aktif güvenlik seviyesi. */
function security_level(): string
{
    return SecurityLevel::get();
}

function is_logged_in(): bool
{
    return Auth::check();
}

function current_user(): ?array
{
    return Auth::user();
}

function require_login(): void
{
    if (!Auth::check()) {
        header('Location: ' . base_url('login.php'));
        exit;
    }
}

/** Tehlikeli labların açık olup olmadığı. */
function dangerous_enabled(): bool
{
    return (bool) ($GLOBALS['config']['enable_dangerous'] ?? false);
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/** Docroot tabanlı URL üretir (alt klasör derinliğinden bağımsız). */
function base_url(string $path = ''): string
{
    return '/' . ltrim($path, '/');
}

/** Bir challenge için deterministik flag üretir. */
function flag_for(string $challengeId): string
{
    $key = $GLOBALS['config']['ctf_key'] ?? 'k';
    return 'FLAG{' . substr(hash_hmac('sha256', $challengeId, $key), 0, 24) . '}';
}

/** Çözümü işaretle (server-side solve detection). */
function mark_solved(string $challengeId): void
{
    Scoreboard::markSolved($challengeId);
}

/**
 * Open redirect tespiti: hedef, tarayıcının site DIŞINA götüreceği bir değer mi?
 * Backslash'leri normalize eder (tarayıcı '\' -> '/' yapar), protocol-relative ('//')
 * ve farklı-host scheme'lerini harici sayar.
 */
function openredirect_external(string $dest): bool
{
    $norm = str_replace('\\', '/', trim($dest));
    if (preg_match('#^//#', $norm)) {
        return true;                       // //evil.tld (protocol-relative)
    }
    if (preg_match('#^[a-z][a-z0-9+.-]*://#i', $norm)) {
        $host = parse_url($norm, PHP_URL_HOST);
        $self = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
        return $host !== null && strcasecmp($host, $self) !== 0;
    }
    return false;
}

/** Basit kural-tabanlı IDS: payload'da saldırı imzası var mı? Logla. */
function ids_log(string $challengeId, string $payload): bool
{
    return Logger::logAttempt($challengeId, $payload);
}

/**
 * XSS solve tespiti: sayfaya basılacak (işlenmiş) çıktıda hâlâ çalıştırılabilir
 * bir HTML/JS imzası kaldıysa exploit başarılı sayılır.
 */
function xss_detect(string $rendered, string $challengeId): bool
{
    if (preg_match('/<script|<svg|<img|<iframe|on\w+\s*=|javascript:/i', $rendered)) {
        mark_solved($challengeId);
        return true;
    }
    return false;
}

/**
 * SSRF solve tespiti: getirilen gövde, yalnızca sunucu tarafından erişilebilecek
 * bir iç kaynağa ulaşıldığını gösteriyorsa (passwd dosyası ya da iç Adminer servisi).
 */
function ssrf_detect(string $body): bool
{
    if (preg_match('/root:.*:0:0:/', $body) || preg_match('/Adminer/i', $body)) {
        mark_solved('ssrf');
        echo '<div class="result">✅ İç kaynağa SSRF ile ulaşıldı — challenge çözüldü!</div>';
        return true;
    }
    return false;
}
