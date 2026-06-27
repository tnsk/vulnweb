<?php
/**
 * Logging — HIGH
 * KASITLI BOZUK SAVUNMA: CRLF temizlenir AMA log GÖRÜNTÜLEYİCİ kayıtları encode etmeden basar
 * → log üzerinden stored XSS (CWE-117 + XSS). Log'u izleyen analist tarayıcısında çalışır.
 * Bypass:  ?user=<img src=x onerror=alert(document.domain)>
 */
$key = 'log9_high';
if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = ['09:00:00 FAIL login user=bob'];
}
$user = $_GET['user'] ?? '';
if ($user !== '') {
    ids_log('logging', $user);
    $clean = str_replace(["\r", "\n"], '', $user);   // CRLF temiz ama encode yok
    $_SESSION[$key][] = date('H:i:s') . ' FAIL login user=' . $clean;
}

echo '<p>CRLF temizleniyor — ama log görüntüleyici kayıtları encode etmiyor.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: log\'a XSS yerleştir — '
   . '<code>&lt;img src=x onerror=alert(1)&gt;</code></p>';
echo '<form method="get">kullanıcı: <input name="user" value=""> <button type="submit">Giriş dene</button></form>';

// ----- ZAFİYETLİ: log görüntüleyici encode ETMEZ -----
$rendered = '<div class="result">' . implode('<br>', $_SESSION[$key]) . '</div>';
echo $rendered;
// -----------------------------------------------------

if (xss_detect($rendered, 'logging')) {
    echo '<div class="result">✅ Log görüntüleyiciye XSS enjekte edildi — challenge çözüldü!</div>';
}
