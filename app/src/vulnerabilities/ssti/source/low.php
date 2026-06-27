<?php
/**
 * SSTI — LOW
 * KASITLI ZAFİYET: kullanıcı girdisi şablon KAYNAĞINA girer ve {{ ifade }} eval edilir.
 * Tespit: {{7*7}} -> 49.   RCE: {{shell_exec('id')}}  ya da  {{`id`}}
 */
$tpl = $_REQUEST['tpl'] ?? 'Merhaba {{7*7}}';

echo '<p>Şablonun render edilir ( <code>{{ ... }}</code> ifade olarak işlenir ):</p>';
echo '<form method="post"><input type="text" name="tpl" size="50" value="' . e($tpl) . '"> <button type="submit">Render</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('ssti', $tpl);
    // ----- ZAFİYETLİ: {{expr}} doğrudan eval -----
    $rendered = preg_replace_callback('/\{\{(.+?)\}\}/', static function ($m) {
        return (string) @eval('return ' . $m[1] . ';');
    }, $tpl);
    // ---------------------------------------------
    echo '<div class="result">' . e($rendered) . '</div>';
    if (preg_match('/uid=\d+\(|root:.*:0:0:/', $rendered)) {
        mark_solved('ssti');
        echo '<div class="result">✅ SSTI → RCE — challenge çözüldü!</div>';
    }
}
