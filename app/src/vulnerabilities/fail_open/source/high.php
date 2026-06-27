<?php
/**
 * Fail-Open — HIGH
 * KASITLI BOZUK SAVUNMA: deny-list yaklaşımı — yalnızca 'guest'/'anonymous' reddedilir,
 * TANINMAYAN her rol kabul edilir (allow-list yerine deny-list = fail-open tasarımı).
 * Bypass:  role'ü 'superuser' gibi tanınmayan bir değer yap.
 *   perm = base64('{"role":"superuser"}')
 */
$perm = $_GET['perm'] ?? '';

$granted = false;
$role = '';
try {
    $data = json_decode(base64_decode($perm, true) ?: '', true, 512, JSON_THROW_ON_ERROR);
    $role = (string) ($data['role'] ?? '');
    // BOZUK: yalnızca bilinen "kötü" roller reddedilir; gerisi serbest
    $denied = ['guest', 'anonymous', ''];
    $granted = !in_array($role, $denied, true);
} catch (\Throwable $e) {
    $granted = false;   // fail-closed
}

echo '<p>Yönetici alanı (yalnızca guest/anonymous reddedilir).</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: tanınmayan bir rol gönder — '
   . '<code>perm = base64(\'{"role":"superuser"}\')</code></p>';
echo '<form method="get">perm: <input name="perm" value="' . e($perm) . '" size="40"> <button type="submit">Eriş</button></form>';

if ($granted) {
    echo '<div class="result"><strong>🔓 YÖNETİCİ ALANI</strong> · gizli: ' . flag_for('fail_open') . '</div>';
    if ($role !== 'admin') {
        ids_log('fail_open', 'role=' . $role);
        mark_solved('fail_open');
        echo '<div class="result">✅ Deny-list (fail-open tasarım) aşıldı — çözüldü!</div>';
    }
} else {
    echo '<div class="result detected">Erişim reddedildi.</div>';
}
