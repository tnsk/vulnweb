<?php
/**
 * Supply Chain — MEDIUM
 * KASITLI BOZUK SAVUNMA: app girdiyi sanitize eder (<,> siler) AMA bu backdoor'u durdurmaz
 * (tetikleyici düz metin). Kendi savunman, bağımlılıktaki backdoor'a karşı işe yaramaz.
 */
$msg = $_REQUEST['msg'] ?? '';

echo '<p>App artık girdiyi "temizliyor" (&lt; &gt; siliniyor) — ama backdoor düz metin tetikleyici kullanıyor.</p>';
echo '<form method="post"><input name="msg" size="60" value="' . e($msg) . '"> <button type="submit">Logla</button></form>';

if ($msg !== '') {
    ids_log('supply_chain', $msg);
    $clean = str_replace(['<', '>'], '', $msg);   // backdoor'u etkilemez
    $out = quicklog_write($clean);
    echo '<div class="result">' . e($out) . '</div>';
    if (preg_match('/uid=\d+\(|root:.*:0:0:/', $out)) {
        mark_solved('supply_chain');
        echo '<div class="result">✅ Kendi sanitizasyonun backdoor\'u durdurmadı — çözüldü!</div>';
    }
}
