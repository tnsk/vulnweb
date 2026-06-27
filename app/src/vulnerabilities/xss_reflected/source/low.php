<?php
/**
 * Reflected XSS — LOW
 * KASITLI ZAFİYET: girdi hiç encode edilmeden cevaba yansıtılır.
 * Payload:  <script>alert(document.domain)</script>
 */
$name = $_GET['name'] ?? '';

echo '<form method="get">İsim: <input type="text" name="name" value=""> <button type="submit">Selamla</button></form>';

if ($name !== '') {
    ids_log('xss_reflected', $name);
    // ----- ZAFİYETLİ: ham echo -----
    $rendered = 'Merhaba ' . $name;
    echo '<div class="result">' . $rendered . '</div>';
    // -------------------------------
    if (xss_detect($rendered, 'xss_reflected')) {
        echo '<div class="result">✅ Payload encode edilmeden yansıdı — challenge çözüldü!</div>';
    }
}
