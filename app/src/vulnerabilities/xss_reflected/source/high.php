<?php
/**
 * Reflected XSS — HIGH
 * KASITLI BOZUK SAVUNMA: <script ... > kalıplarını regex ile siler (case-insensitive)
 * ama olay-handler tabanlı vektörler kalır.
 * Bypass:  <svg onload=alert(document.domain)>  /  <img src=x onerror=...>
 */
$name = $_GET['name'] ?? '';

echo '<form method="get">İsim: <input type="text" name="name" value=""> <button type="submit">Selamla</button></form>';

if ($name !== '') {
    ids_log('xss_reflected', $name);
    // ----- BOZUK SAVUNMA: sadece script etiketini hedef alır -----
    $filtered = preg_replace('/<\s*\/?\s*script[^>]*>/i', '', $name);
    // -------------------------------------------------------------
    $rendered = 'Merhaba ' . $filtered;
    echo '<div class="result">' . $rendered . '</div>';
    if (xss_detect($rendered, 'xss_reflected')) {
        echo '<div class="result">✅ Olay-handler vektörü ile aşıldı — challenge çözüldü!</div>';
    }
}
