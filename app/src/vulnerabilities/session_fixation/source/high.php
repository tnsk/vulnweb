<?php
/**
 * Session Fixation — HIGH
 * KASITLI BOZUK SAVUNMA: ?sid yalnızca "geçerli formatta" (alfanümerik) ise kabul edilir.
 * Format doğrulama fixation'ı durdurmaz; saldırgan geçerli-formatta bir sid verir.
 * Bypass: ?sid=ATTACKERFIXED42  (alfanümerik). Hâlâ girişte regenerate yok.
 */
if (isset($_GET['sid'])) {
    $sid = (string) $_GET['sid'];
    if (preg_match('/^[A-Za-z0-9]{8,64}$/', $sid)) {     // BOZUK: format kontrolü
        $_SESSION['fx_sid']   = $sid;
        $_SESSION['fx_fixed'] = $sid;
    }
}
if (empty($_SESSION['fx_sid'])) {
    $_SESSION['fx_sid'] = bin2hex(random_bytes(6));
}

if (($_POST['action'] ?? '') === 'login') {
    $_SESSION['fx_auth'] = true;            // regenerate YOK (asıl eksik)
}
if (($_POST['action'] ?? '') === 'logout') {
    unset($_SESSION['fx_auth']);
}

$authed = !empty($_SESSION['fx_auth']);
echo '<p>fx_sid: <code>' . e($_SESSION['fx_sid']) . '</code> (format doğrulanır) · '
   . ($authed ? '<strong>giriş yapıldı</strong>' : 'anonim') . '</p>';
echo '<p><a href="?sid=ATTACKERFIXED42">?sid=ATTACKERFIXED42</a> → giriş:</p>';
echo '<form method="post"><button name="action" value="login">Giriş yap</button> '
   . '<button name="action" value="logout">Çıkış</button></form>';

if ($authed && !empty($_SESSION['fx_fixed']) && hash_equals($_SESSION['fx_fixed'], $_SESSION['fx_sid'])) {
    ids_log('session_fixation', 'fixed=' . $_SESSION['fx_fixed']);
    mark_solved('session_fixation');
    echo '<div class="result">✅ Format doğrulama fixation\'ı durdurmaz — çözüldü!</div>';
}
