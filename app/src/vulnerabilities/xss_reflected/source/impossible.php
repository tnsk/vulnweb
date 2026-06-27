<?php
/**
 * Reflected XSS — IMPOSSIBLE (güvenli referans)
 * Context-aware output encoding: htmlspecialchars(ENT_QUOTES|ENT_HTML5).
 * (Ek olarak sıkı bir Content-Security-Policy önerilir.)
 */
$name = $_GET['name'] ?? '';

echo '<form method="get">İsim: <input type="text" name="name" value=""> <button type="submit">Selamla</button></form>';

if ($name !== '') {
    // ----- GÜVENLİ: bağlama uygun encoding -----
    $safe = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // -------------------------------------------
    echo '<div class="result">Merhaba ' . $safe . '</div>';
}
