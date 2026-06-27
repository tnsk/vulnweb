<?php
/**
 * Deserialization — MEDIUM
 * KASITLI BOZUK SAVUNMA: serialize string'inde 'system|exec|shell' kelimelerini arar.
 * Etkisiz çünkü tehlikeli fonksiyon GADGET KODUNDA, veride değil. Bypass: cmd="cat /etc/passwd".
 */
$data = $_REQUEST['data'] ?? '';

echo '<p>"Savunma": payload tehlikeli kelime içeriyorsa reddedilir.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>cmd</code> değerinde yasak kelime kullanma — '
   . 'örn. <code>' . e(deser_sample_payload('cat /etc/passwd')) . '</code></p>';
echo '<form method="post"><input type="text" name="data" size="70" value="' . e($data) . '"> <button type="submit">Gönder</button></form>';

if ($data !== '') {
    ids_log('deserialization', $data);
    // BOZUK: serialize verisinde keyword araması (anlamsız)
    if (preg_match('/system|exec|shell|passthru|popen/i', $data)) {
        echo '<div class="result detected">Reddedildi: tehlikeli kelime tespit edildi.</div>';
    } else {
        $obj = @unserialize($data);
        deser_show_result($obj);
    }
}
