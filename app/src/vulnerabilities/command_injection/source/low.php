<?php
/**
 * Command Injection — LOW
 * KASITLI ZAFİYET: girdi shell komutuna doğrudan eklenir.
 * Payload:  127.0.0.1; cat /etc/passwd
 */
$ip = $_REQUEST['ip'] ?? '';

echo '<form method="post">IP/host: <input type="text" name="ip" value="' . e($ip) . '" placeholder="127.0.0.1"> <button type="submit">Ping</button></form>';

if ($ip !== '') {
    ids_log('command_injection', $ip);
    // ----- ZAFİYETLİ: ham concat shell'e -----
    $cmd = 'ping -c 4 ' . $ip;
    $out = (string) shell_exec($cmd . ' 2>&1');
    // -----------------------------------------
    echo '<div class="result"><strong>$ ' . e($cmd) . "</strong>\n\n" . e($out) . '</div>';
    if (preg_match('/root:.*:0:0:|uid=\d+\(/', $out)) {
        mark_solved('command_injection');
        echo '<div class="result">✅ Komut enjeksiyonu başarılı — challenge çözüldü!</div>';
    }
}
