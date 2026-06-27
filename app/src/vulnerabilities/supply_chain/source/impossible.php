<?php
/**
 * Supply Chain — IMPOSSIBLE (güvenli referans)
 * - Bağımlılık denetlenmiş, sabitlenmiş (pinned) TEMİZ sürümle değiştirildi (SafeLog 3.0.0).
 * - Integrity hash ile doğrulama (composer.lock / SRI mantığı).
 * - SCA (Software Composition Analysis) CI'da çalıştırılır; bilinen CVE'li sürüm reddedilir.
 */
$msg = $_REQUEST['msg'] ?? '';

// Integrity kontrolü (gerçekte composer.lock hash'i / imza doğrulanır)
$expectedVersion = '3.0.0';
if (safelog_version() !== $expectedVersion) {
    die('Bağımlılık integrity hatası: beklenmeyen sürüm.');
}

echo '<p>Bağımlılık: <code>safelog/safelog ' . e(safelog_version())
   . '</code> — denetlenmiş, backdoor yok, hash ile sabitlenmiş.</p>';
echo '<form method="post"><input name="msg" size="60" value="" placeholder="__qlog_exec__:system(\'id\');"> <button type="submit">Logla</button></form>';

if ($msg !== '') {
    // ----- GÜVENLİ: temiz kütüphane, kod yürütme yok -----
    $out = safelog_write($msg);
    // -----------------------------------------------------
    echo '<div class="result ok">' . e($out) . ' (backdoor yok)</div>';
}
