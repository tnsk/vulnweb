<?php
/**
 * Security Misconfiguration — IMPOSSIBLE (güvenli referans)
 * - Debug endpoint'i yok; secret'ler asla ifşa edilmez.
 * - Generic hata mesajı (detay sunucu-tarafı log'a, ekrana değil).
 * - Güvenlik header'ları gönderilir.
 */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'");
header('Referrer-Policy: no-referrer');

echo '<p>Güvenli tablo görüntüleyici (allowlist + generic hata).</p>';
echo '<form method="get">tablo: <select name="q"><option>users</option><option>guestbook</option></select> <button type="submit">Göster</button></form>';

if (isset($_GET['q'])) {
    $allowed = ['users', 'guestbook'];
    if (!in_array($_GET['q'], $allowed, true)) {
        echo '<div class="result">Geçersiz istek.</div>';   // generic
    } else {
        try {
            $stmt = DB::pdo()->query('SELECT id FROM ' . $_GET['q'] . ' LIMIT 3');
            echo '<div class="result ok">' . e(var_export($stmt->fetchAll(), true)) . '</div>';
        } catch (\Throwable $ex) {
            error_log('misconfig lab: ' . $ex->getMessage());   // log'a, ekrana değil
            echo '<div class="result">Bir hata oluştu. Lütfen tekrar deneyin.</div>';
        }
    }
}
