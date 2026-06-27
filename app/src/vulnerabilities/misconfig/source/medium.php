<?php
/**
 * Security Misconfiguration — MEDIUM
 * KASITLI BOZUK SAVUNMA: debug paneli zayıf/tahmin edilebilir bir değerle "gizlenir".
 * Bypass:  ?debug=secret
 */
echo '<p>Debug paneli artık bir "anahtar" ister (güya güvenli).</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: anahtar çok yaygın bir kelime — <code>?debug=secret</code></p>';

if (isset($_GET['debug'])) {
    ids_log('misconfig', 'debug=' . $_GET['debug']);
    if ($_GET['debug'] === 'secret') {
        $db = $GLOBALS['config']['db'];
        echo '<div class="result"><strong>DEBUG INFO</strong>' . "\n";
        echo "DB: {$db['user']}:{$db['pass']}@{$db['host']}\n";
        echo 'Gizli: ' . flag_for('misconfig') . "\n";
        echo '</div>';
        mark_solved('misconfig');
        echo '<div class="result">✅ Tahmin edilebilir debug anahtarı — çözüldü!</div>';
    } else {
        echo '<div class="result detected">Geçersiz debug anahtarı.</div>';
    }
}
