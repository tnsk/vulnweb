<?php
/**
 * Software Supply Chain Failures — LOW
 * KASITLI ZAFİYET: backdoor'lanmış üçüncü taraf kütüphane (QuickLog 2.1.3) kullanılır.
 * Senin kodun temiz; zafiyet bağımlılıkta. SCA aracı bunu CVE olarak işaretler.
 * Sömürü:  msg = __qlog_exec__:system('id');
 */
$msg = $_REQUEST['msg'] ?? '';

echo '<p>Bağımlılık: <code>quicklog/quicklog ' . e(quicklog_version())
   . '</code> (manifest\'te listeli). Senin kodun sadece <code>quicklog_write($msg)</code> çağırıyor.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: bağımlılıkta gizli bir tetikleyici var — '
   . '<code>__qlog_exec__:system(\'id\');</code></p>';
echo '<form method="post"><input name="msg" size="60" value="' . e($msg) . '"> <button type="submit">Logla</button></form>';

if ($msg !== '') {
    ids_log('supply_chain', $msg);
    // ----- Senin kodun "masum": sadece kütüphaneyi çağırır -----
    $out = quicklog_write($msg);
    // -----------------------------------------------------------
    echo '<div class="result">' . e($out) . '</div>';
    if (preg_match('/uid=\d+\(|root:.*:0:0:/', $out)) {
        mark_solved('supply_chain');
        echo '<div class="result">✅ Backdoor\'lu bağımlılık üzerinden RCE — challenge çözüldü!</div>';
    }
}
