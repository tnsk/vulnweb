<?php
/**
 * Open Redirect — IMPOSSIBLE (güvenli referans)
 * Allowlist: kullanıcı opak bir ANAHTAR seçer; sunucu sabit iç hedefe yönlendirir.
 * Kullanıcı girdisi asla doğrudan Location'a gitmez.
 */
$targets = [
    'home'  => '/index.php',
    'score' => '/scoreboard.php',
];
$key = (string) ($_GET['to'] ?? '');

echo '<p>Güvenli: yalnızca önceden tanımlı hedefler.</p>';
echo '<p><a href="?to=home">home</a> · <a href="?to=score">score</a></p>';

if ($key !== '') {
    $dest = $targets[$key] ?? '/index.php';   // allowlist; bilinmeyen -> güvenli varsayılan
    echo '<div class="result">Güvenli yönlendirme hedefi: ' . e($dest)
       . ' <a href="' . e($dest) . '">Devam »</a></div>';
}
