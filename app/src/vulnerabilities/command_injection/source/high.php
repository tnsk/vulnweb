<?php
/**
 * Command Injection — HIGH
 * KASITLI BOZUK SAVUNMA: geniş ayraç blocklist'i ama newline (\n) unutulmuş.
 * Bypass:  127.0.0.1%0acat /etc/passwd   (URL-encoded newline ile komut ayır)
 */
$ip = $_REQUEST['ip'] ?? '';

echo '<form method="post">IP/host: <input type="text" name="ip" value="' . e($ip) . '" placeholder="127.0.0.1"> <button type="submit">Ping</button></form>';

if ($ip !== '') {
    ids_log('command_injection', $ip);
    // BOZUK: çok sayıda ayraç silinir ama satır-sonu (\n) atlanmış
    $blacklist = ['&', ';', '|', '$', '(', ')', '`', '||', '&&', '{', '}', '<', '>'];
    $clean = str_replace($blacklist, '', $ip);
    $cmd = 'ping -c 4 ' . $clean;
    $out = (string) shell_exec($cmd . ' 2>&1');
    echo '<div class="result"><strong>$ ' . e($cmd) . "</strong>\n\n" . e($out) . '</div>';
    if (preg_match('/root:.*:0:0:|uid=\d+\(/', $out)) {
        mark_solved('command_injection');
        echo '<div class="result">✅ Newline enjeksiyonu ile aşıldı — challenge çözüldü!</div>';
    }
}
