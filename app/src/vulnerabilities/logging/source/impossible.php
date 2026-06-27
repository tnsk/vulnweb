<?php
/**
 * Logging — IMPOSSIBLE (güvenli referans)
 * - Yapılandırılmış log (alanlar ayrı, kullanıcı girdisi nötrlenir: CRLF + encode).
 * - Görüntülemede htmlspecialchars.
 * - Üretimde: ALERTING — tekrarlı başarısız girişlerde uyarı tetiklenir (A09 "Alerting").
 */
$key = 'log9_imp';
if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = [['ts' => '09:00:00', 'status' => 'FAIL', 'user' => 'bob']];
}
$user = $_GET['user'] ?? '';
if ($user !== '') {
    // CRLF ve kontrol karakterlerini temizle; alanı veri olarak sakla
    $safe = preg_replace('/[\r\n\x00-\x1f]/', '', $user);
    $_SESSION[$key][] = ['ts' => date('H:i:s'), 'status' => 'FAIL', 'user' => $safe];

    // ALERTING: aynı oturumda çok sayıda FAIL → uyarı
    $fails = count(array_filter($_SESSION[$key], fn($e) => $e['status'] === 'FAIL'));
    if ($fails >= 5) {
        echo '<div class="result detected">⚠ ALERT: çok sayıda başarısız giriş tespit edildi.</div>';
    }
}

echo '<p>Güvenli yapılandırılmış log (alanlar nötrlenir + encode edilir).</p>';
echo '<form method="get">kullanıcı: <input name="user" value=""> <button type="submit">Giriş dene</button></form>';

echo '<pre class="result">';
foreach ($_SESSION[$key] as $e) {
    // GÜVENLİ: her alan encode edilir, enjeksiyon imkânsız
    echo e($e['ts']) . ' ' . e($e['status']) . ' login user=' . e($e['user']) . "\n";
}
echo '</pre>';
