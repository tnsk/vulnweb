<?php
/**
 * Security Misconfiguration — LOW
 * KASITLI ZAFİYET: açık debug endpoint'i DB kimliklerini ve gizli bilgiyi sızdırır.
 * Sömürü:  ?debug=1
 */
echo '<p>Hata ayıklama paneli: <a href="?debug=1">?debug=1</a></p>';

if (isset($_GET['debug'])) {
    ids_log('misconfig', 'debug');
    $db = $GLOBALS['config']['db'];
    echo '<div class="result"><strong>DEBUG INFO (açıkta!)</strong>' . "\n";
    echo 'PHP: ' . PHP_VERSION . "\n";
    echo "DB host: {$db['host']}  user: {$db['user']}  pass: {$db['pass']}\n";
    echo 'Varsayılan kimlik: admin / password' . "\n";
    echo 'Gizli: ' . flag_for('misconfig') . "\n";
    echo '</div>';
    mark_solved('misconfig');
    echo '<div class="result">✅ Açık debug paneli sızdırıldı — challenge çözüldü!</div>';
}
