<?php
/**
 * Mishandling of Exceptional Conditions (Fail-Open) — LOW
 * KASITLI ZAFİYET: yetki kontrolü bir istisna atınca erişim AÇILIR (fail-open).
 * Normalde reddedilir; ama bozuk bir 'perm' token'ı istisna tetikleyip kapıyı açar.
 * Sömürü:  ?perm=GARBAGE  (geçersiz token → istisna → erişim verildi)
 */
$perm = $_GET['perm'] ?? '';

$granted = false;
$viaException = false;
try {
    // Geçerli token base64(json) olmalı; bozuksa json_decode istisna atar.
    $json = base64_decode($perm, true);
    if ($json === false) {
        throw new \RuntimeException('bad base64');
    }
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    $granted = (($data['role'] ?? '') === 'admin');   // kullanıcı meşru admin token'a sahip değil
} catch (\Throwable $e) {
    $granted = true;          // ----- KASITLI FAIL-OPEN -----
    $viaException = true;
}

echo '<p>Yönetici alanı yetki token\'ı ister.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: geçersiz bir token gönder de hata yönetimine bak — <code>?perm=GARBAGE</code></p>';
echo '<form method="get">perm: <input name="perm" value="' . e($perm) . '"> <button type="submit">Eriş</button></form>';

if ($granted) {
    echo '<div class="result"><strong>🔓 YÖNETİCİ ALANI</strong> · gizli: ' . flag_for('fail_open') . '</div>';
    if ($viaException) {
        ids_log('fail_open', $perm);
        mark_solved('fail_open');
        echo '<div class="result">✅ İstisna → fail-open ile yetki kazanıldı — çözüldü!</div>';
    }
} else {
    echo '<div class="result detected">Erişim reddedildi.</div>';
}
