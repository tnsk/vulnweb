<?php
/**
 * SSTI — MEDIUM
 * KASITLI BOZUK SAVUNMA: ifadede 'system|exec|shell|passthru' kelimeleri bloklanır.
 * Bypass: backtick operatörü —  {{`id`}}  (kelime içermez, shell_exec'e eşdeğer).
 */
$tpl = $_REQUEST['tpl'] ?? 'Merhaba {{7*7}}';

echo '<p>"Savunma": tehlikeli fonksiyon adları bloklanır.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: kelime kullanmadan komut çalıştır — <code>{{`id`}}</code></p>';
echo '<form method="post"><input type="text" name="tpl" size="50" value="' . e($tpl) . '"> <button type="submit">Render</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('ssti', $tpl);
    if (preg_match('/system|exec|shell|passthru|popen/i', $tpl)) {
        echo '<div class="result detected">Reddedildi: tehlikeli fonksiyon adı.</div>';
    } else {
        $rendered = preg_replace_callback('/\{\{(.+?)\}\}/', static function ($m) {
            return (string) @eval('return ' . $m[1] . ';');
        }, $tpl);
        echo '<div class="result">' . e($rendered) . '</div>';
        if (preg_match('/uid=\d+\(|root:.*:0:0:/', $rendered)) {
            mark_solved('ssti');
            echo '<div class="result">✅ Backtick ile blocklist aşıldı — çözüldü!</div>';
        }
    }
}
