<?php
/**
 * Logging — MEDIUM
 * KASITLI BOZUK SAVUNMA: yalnızca "\n" (LF) silinir.
 * Bypass: "\r" (CR) ile enjekte et —  ?user=eve%0d00:00:00 SUCCESS login user=admin
 */
$key = 'log9_medium';
if (!isset($_SESSION[$key])) {
    $_SESSION[$key] = ['09:00:00 FAIL login user=bob'];
}
$user = $_GET['user'] ?? '';
if ($user !== '') {
    ids_log('logging', $user);
    $clean = str_replace("\n", '', $user);   // BOZUK: sadece LF
    $_SESSION[$key][] = date('H:i:s') . ' FAIL login user=' . $clean;
}

echo '<p>"\n" siliniyor (eksik nötrleme).</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>\r</code> (CR) hâlâ geçiyor — <code>%0d</code>.</p>';
echo '<form method="get">kullanıcı: <input name="user" value=""> <button type="submit">Giriş dene</button></form>';

$text = implode("\n", $_SESSION[$key]);
echo '<pre class="result">' . e($text) . '</pre>';

if (preg_match('/\bSUCCESS\b/', $text)) {
    mark_solved('logging');
    echo '<div class="result">✅ CR ile log injection — challenge çözüldü!</div>';
}
