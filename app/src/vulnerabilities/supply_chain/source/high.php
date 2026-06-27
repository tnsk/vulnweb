<?php
/**
 * Supply Chain — HIGH
 * KASITLI BOZUK SAVUNMA: app, tehlikeli fonksiyon adlarını (system/exec/shell) bloklar
 * AMA backdoor eval'i her PHP ifadesini çalıştırır → bu kelimeler olmadan da RCE/dosya okuma.
 * Bypass:  __qlog_exec__:readfile('/etc/passwd');   ya da   __qlog_exec__:print(`id`);
 */
$msg = $_REQUEST['msg'] ?? '';

echo '<p>App tehlikeli fonksiyon adlarını bloklar (system/exec/shell/passthru).</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: bu kelimeler olmadan da çalıştır — '
   . '<code>__qlog_exec__:readfile(\'/etc/passwd\');</code></p>';
echo '<form method="post"><input name="msg" size="60" value="' . e($msg) . '"> <button type="submit">Logla</button></form>';

if ($msg !== '') {
    ids_log('supply_chain', $msg);
    if (preg_match('/system|exec|shell|passthru|popen/i', $msg)) {
        echo '<div class="result detected">Reddedildi: tehlikeli fonksiyon adı.</div>';
    } else {
        $out = quicklog_write($msg);
        echo '<div class="result">' . e($out) . '</div>';
        if (preg_match('/uid=\d+\(|root:.*:0:0:/', $out)) {
            mark_solved('supply_chain');
            echo '<div class="result">✅ Blocklist\'siz fonksiyonla backdoor tetiklendi — çözüldü!</div>';
        }
    }
}
