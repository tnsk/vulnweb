<?php
/**
 * Session Fixation — IMPOSSIBLE (güvenli referans)
 * - İstemciden gelen ?sid ASLA kabul edilmez (sunucu kontrolünde kimlik).
 * - Girişte (privilege artışı) oturum kimliği YENİDEN ÜRETİLİR; eski geçersizleşir.
 * - Üretimde: cookie HttpOnly + Secure + SameSite=Strict.
 */
if (empty($_SESSION['fx_sid'])) {
    $_SESSION['fx_sid'] = bin2hex(random_bytes(12));
}
// ?sid bilinçli olarak YOK SAYILIR (fixation vektörü kapalı)

if (($_POST['action'] ?? '') === 'login') {
    $_SESSION['fx_sid']  = bin2hex(random_bytes(12));   // GÜVENLİ: girişte regenerate
    $_SESSION['fx_auth'] = true;
}
if (($_POST['action'] ?? '') === 'logout') {
    unset($_SESSION['fx_auth']);
    $_SESSION['fx_sid'] = bin2hex(random_bytes(12));
}

$authed = !empty($_SESSION['fx_auth']);
echo '<p>fx_sid: <code>' . e($_SESSION['fx_sid']) . '</code> (yalnızca sunucu üretir) · '
   . ($authed ? '<strong>giriş yapıldı</strong>' : 'anonim') . '</p>';
echo '<p style="color:var(--muted);font-size:.85rem">?sid yok sayılır; girişte kimlik değişir. '
   . 'Sabitleme denemesi (<code>?sid=ATTACKERFIXED42</code>) etkisizdir.</p>';
echo '<form method="post"><button name="action" value="login">Giriş yap</button> '
   . '<button name="action" value="logout">Çıkış</button></form>';
