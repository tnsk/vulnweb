<?php
/**
 * Insecure Deserialization / PHP Object Injection — LOW
 * KASITLI ZAFİYET: güvenilmeyen veri doğrudan unserialize edilir → Pwn::__wakeup() RCE.
 * Payload (data):  O:3:"Pwn":2:{s:3:"cmd";s:2:"id";s:6:"result";s:0:"";}
 */
$data = $_REQUEST['data'] ?? '';

echo '<p>Sunucu, <code>data</code> alanını <code>unserialize()</code> ile çözüyor.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">Hazır payload (cmd=id): <code>' . e(deser_sample_payload('id')) . '</code></p>';
echo '<form method="post"><input type="text" name="data" size="70" value="' . e($data) . '"> <button type="submit">Gönder</button></form>';

if ($data !== '') {
    ids_log('deserialization', $data);
    // ----- ZAFİYETLİ: ham unserialize -----
    $obj = @unserialize($data);
    // ---------------------------------------
    deser_show_result($obj);
}
