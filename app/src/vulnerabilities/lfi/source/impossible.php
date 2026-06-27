<?php
/**
 * LFI/RFI — IMPOSSIBLE (güvenli referans)
 * Allowlist mapping: kullanıcı girdisi ASLA include()'a gitmez; sadece anahtar→dosya eşlemesi.
 * (Ayrıca allow_url_include=Off önerilir.)
 */
$pages = [
    'home'  => 'pages/home.php',
    'about' => 'pages/about.php',
];
$key = (string) ($_GET['page'] ?? 'home');

echo '<p>Sayfalar: <a href="?page=home">home</a> · <a href="?page=about">about</a></p>';

echo '<div class="result">';
ob_start();
include $pages[$key] ?? 'pages/home.php';   // yalnızca allowlist değeri
echo ob_get_clean();
echo '</div>';
