<?php
/**
 * Reflected XSS — MEDIUM
 * KASITLI BOZUK SAVUNMA: yalnızca "<script>" stringini siler (blocklist).
 * Bypass:  <img src=x onerror=alert(document.domain)>  veya  <ScRiPt>...
 */
$name = $_GET['name'] ?? '';

echo '<form method="get">İsim: <input type="text" name="name" value=""> <button type="submit">Selamla</button></form>';

if ($name !== '') {
    ids_log('xss_reflected', $name);
    // ----- BOZUK SAVUNMA: birebir "<script>" sil (case-sensitive, tek kalıp) -----
    $filtered = str_replace('<script>', '', $name);
    // ----------------------------------------------------------------------------
    $rendered = 'Merhaba ' . $filtered;
    echo '<div class="result">' . $rendered . '</div>';
    if (xss_detect($rendered, 'xss_reflected')) {
        echo '<div class="result">✅ Blocklist aşıldı — challenge çözüldü!</div>';
    }
}
