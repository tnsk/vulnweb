<?php
/**
 * Session Fixation — LOW
 * KASITLI ZAFİYET: lab oturum kimliği URL'den (?sid) kabul edilir ve girişte
 * YENİDEN ÜRETİLMEZ. Saldırgan kurbana sabit bir sid verir; kurban giriş yapınca
 * saldırgan o oturumu sürer.
 * Sömürü:  ?sid=ATTACKER_FIXED_42  ardından "Giriş yap".
 */
if (isset($_GET['sid'])) {
    $_SESSION['fx_sid']   = $_GET['sid'];
    $_SESSION['fx_fixed'] = $_GET['sid'];   // dışarıdan sabitlenen değer
}
if (empty($_SESSION['fx_sid'])) {
    $_SESSION['fx_sid'] = bin2hex(random_bytes(6));
}

if (($_POST['action'] ?? '') === 'login') {
    $_SESSION['fx_auth'] = true;            // KASITLI: session_regenerate_id YOK
}
if (($_POST['action'] ?? '') === 'logout') {
    unset($_SESSION['fx_auth']);
}

$authed = !empty($_SESSION['fx_auth']);
echo '<p>Lab oturum kimliği (fx_sid): <code>' . e($_SESSION['fx_sid']) . '</code> · Durum: '
   . ($authed ? '<strong>giriş yapıldı</strong>' : 'anonim') . '</p>';
echo '<p>1) Saldırgan linki: <a href="?sid=ATTACKER_FIXED_42">?sid=ATTACKER_FIXED_42</a> &nbsp; '
   . '2) sonra giriş yap:</p>';
echo '<form method="post"><button name="action" value="login">Giriş yap</button> '
   . '<button name="action" value="logout">Çıkış</button></form>';

if ($authed && !empty($_SESSION['fx_fixed']) && hash_equals($_SESSION['fx_fixed'], $_SESSION['fx_sid'])) {
    ids_log('session_fixation', 'fixed=' . $_SESSION['fx_fixed']);
    mark_solved('session_fixation');
    echo '<div class="result">✅ Sabitlenen oturumla giriş yapıldı (regenerate yok) — çözüldü!</div>';
}
