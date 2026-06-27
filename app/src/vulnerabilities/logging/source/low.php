<?php
/**
 * Security Logging & Alerting Failures — LOW
 * KASITLI ZAFİYET: kullanıcı girdisi log satırına ham eklenir (CWE-117 Log Injection).
 * Satır-sonu enjekte ederek SAHTE bir "başarılı admin girişi" log kaydı uydur.
 * Sömürü:  ?user=eve%0a00:00:00 SUCCESS login user=admin
 */
$key = 'log9_low';
if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = ['09:00:00 FAIL login user=bob', '09:01:00 FAIL login user=alice'];
}
$user = $_GET['user'] ?? '';
if ($user !== '') {
    ids_log('logging', $user);
    // ----- ZAFİYETLİ: ham girdi log satırına -----
    $_SESSION[$key][] = date('H:i:s') . ' FAIL login user=' . $user;
    // ---------------------------------------------
}

echo '<p>Başarısız giriş logu. Kullanıcı adı log\'a yazılıyor.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: satır-sonu enjekte et — '
   . '<code>?user=eve%0a00:00:00 SUCCESS login user=admin</code></p>';
echo '<form method="get">kullanıcı: <input name="user" value=""> <button type="submit">Giriş dene</button></form>';

$text = implode("\n", $_SESSION[$key]);
echo '<pre class="result">' . e($text) . '</pre>';

if (preg_match('/\bSUCCESS\b/', $text)) {
    mark_solved('logging');
    echo '<div class="result">✅ Sahte "SUCCESS" log kaydı enjekte edildi — challenge çözüldü!</div>';
}
