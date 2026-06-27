<?php
/**
 * SSTI — HIGH
 * KASITLI BOZUK SAVUNMA: backtick ve birçok fonksiyon bloklanır AMA dosya okuma fonksiyonları kalır.
 * Bypass:  {{file_get_contents('/etc/passwd')}}
 */
$tpl = $_REQUEST['tpl'] ?? 'Merhaba {{7*7}}';

echo '<p>"Savunma": backtick ve shell fonksiyonları bloklanır.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: shell\'e gerek yok — <code>{{file_get_contents(\'/etc/passwd\')}}</code></p>';
echo '<form method="post"><input type="text" name="tpl" size="50" value="' . e($tpl) . '"> <button type="submit">Render</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('ssti', $tpl);
    if (preg_match('/system|exec|shell|passthru|popen|`/i', $tpl)) {
        echo '<div class="result detected">Reddedildi: tehlikeli ifade.</div>';
    } else {
        $rendered = preg_replace_callback('/\{\{(.+?)\}\}/', static function ($m) {
            return (string) @eval('return ' . $m[1] . ';');
        }, $tpl);
        echo '<div class="result">' . e($rendered) . '</div>';
        if (preg_match('/uid=\d+\(|root:.*:0:0:/', $rendered)) {
            mark_solved('ssti');
            echo '<div class="result">✅ Dosya okuma fonksiyonu ile aşıldı — çözüldü!</div>';
        }
    }
}
