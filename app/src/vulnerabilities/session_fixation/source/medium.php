<?php
/**
 * Session Fixation — MEDIUM
 * KASITLI BOZUK SAVUNMA: cookie HttpOnly olarak işaretlenir (XSS hırsızlığına karşı)
 * AMA bu fixation'ı durdurmaz: ?sid hâlâ kabul edilir, girişte regenerate yok.
 * Ders: HttpOnly ≠ fixation koruması. Tek çözüm girişte regenerate.
 */
if (isset($_GET['sid'])) {
    $_SESSION['fx_sid']   = $_GET['sid'];
    $_SESSION['fx_fixed'] = $_GET['sid'];
}
if (empty($_SESSION['fx_sid'])) {
    $_SESSION['fx_sid'] = bin2hex(random_bytes(6));
}

if (($_POST['action'] ?? '') === 'login') {
    $_SESSION['fx_auth'] = true;            // hâlâ regenerate YOK
}
if (($_POST['action'] ?? '') === 'logout') {
    unset($_SESSION['fx_auth']);
}

$authed = !empty($_SESSION['fx_auth']);
echo '<p>fx_sid: <code>' . e($_SESSION['fx_sid']) . '</code> (cookie HttpOnly) · '
   . ($authed ? '<strong>giriş yapıldı</strong>' : 'anonim') . '</p>';
echo '<p><a href="?sid=ATTACKER_FIXED_42">?sid=ATTACKER_FIXED_42</a> → giriş:</p>';
echo '<form method="post"><button name="action" value="login">Giriş yap</button> '
   . '<button name="action" value="logout">Çıkış</button></form>';

if ($authed && !empty($_SESSION['fx_fixed']) && hash_equals($_SESSION['fx_fixed'], $_SESSION['fx_sid'])) {
    ids_log('session_fixation', 'fixed=' . $_SESSION['fx_fixed']);
    mark_solved('session_fixation');
    echo '<div class="result">✅ HttpOnly fixation\'ı durdurmaz — çözüldü!</div>';
}
