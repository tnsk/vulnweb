<?php
/**
 * Deserialization — HIGH
 * KASITLI BOZUK SAVUNMA: allowed_classes allowlist'i AMA gadget sınıfı (Pwn) allowlist'te.
 * Allowlist'e gadget koymak savunma değildir. Bypass: aynı Pwn payload'u.
 */
$data = $_REQUEST['data'] ?? '';

echo '<p>"Savunma": yalnızca izinli sınıflar (<code>Pwn</code>) deserialize edilir.</p>';
echo '<p style="color:var(--muted);font-size:.85rem"><code>' . e(deser_sample_payload('id')) . '</code></p>';
echo '<form method="post"><input type="text" name="data" size="70" value="' . e($data) . '"> <button type="submit">Gönder</button></form>';

if ($data !== '') {
    ids_log('deserialization', $data);
    // BOZUK: gadget'ın kendisi allowlist'te
    $obj = @unserialize($data, ['allowed_classes' => ['Pwn']]);
    deser_show_result($obj);
}
