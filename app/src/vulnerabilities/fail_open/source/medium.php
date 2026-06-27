<?php
/**
 * Fail-Open — MEDIUM
 * KASITLI BOZUK SAVUNMA: istisnada reddedilir AMA token BOŞ/eksikse "varsayılan olarak güven".
 * Bypass:  ?perm=  (boş token → fail-open varsayılan)
 */
$perm = $_GET['perm'] ?? null;

$granted = false;
$broken  = false;
if ($perm === null || $perm === '') {
    $granted = true;   // ----- KASITLI: eksik token = güven -----
    $broken = true;
} else {
    try {
        $data = json_decode(base64_decode($perm, true) ?: '', true, 512, JSON_THROW_ON_ERROR);
        $granted = (($data['role'] ?? '') === 'admin');
    } catch (\Throwable $e) {
        $granted = false;   // bu sefer fail-closed
    }
}

echo '<p>Yönetici alanı (boş token "güvenli içeri" sayılır).</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: token\'ı hiç gönderme — <code>?perm=</code></p>';
echo '<form method="get">perm: <input name="perm" value=""> <button type="submit">Eriş</button></form>';

if ($granted) {
    echo '<div class="result"><strong>🔓 YÖNETİCİ ALANI</strong> · gizli: ' . flag_for('fail_open') . '</div>';
    if ($broken) {
        ids_log('fail_open', 'empty-token');
        mark_solved('fail_open');
        echo '<div class="result">✅ Eksik-token fail-open\'ı ile geçildi — çözüldü!</div>';
    }
} else {
    echo '<div class="result detected">Erişim reddedildi.</div>';
}
