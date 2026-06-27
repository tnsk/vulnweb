<?php
/**
 * XXE — HIGH
 * KASITLI BOZUK SAVUNMA: 'file:' şeması bloklanır.
 * Bypass: şemasız yol kullan —  <!ENTITY xxe SYSTEM "/etc/passwd">  (libxml dosya olarak açar)
 *         ya da  php://filter/convert.base64-encode/resource=/etc/passwd
 */
$default = "<?xml version=\"1.0\"?>\n<!DOCTYPE r [<!ENTITY xxe SYSTEM \"/etc/passwd\">]>\n<r>&xxe;</r>";
$xml = $_POST['xml'] ?? $default;

echo '<p>"Savunma": <code>file:</code> şeması yasak.</p>';
echo '<form method="post"><textarea name="xml" rows="5" cols="70">' . e($xml) . '</textarea><br><button type="submit">Parse</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('xxe', $xml);
    if (stripos($xml, 'file:') !== false) {
        echo '<div class="result detected">Reddedildi: file: şeması yasak.</div>';
    } else {
        libxml_use_internal_errors(true);
        $doc = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_DTDLOAD);
        if ($doc === false) {
            echo '<div class="result detected">XML parse hatası.</div>';
        } else {
            $content = (string) $doc;
            echo '<div class="result">' . e($content) . '</div>';
            if (preg_match('/root:.*:0:0:/', $content)) {
                mark_solved('xxe');
                echo '<div class="result">✅ Şemasız yol ile aşıldı — challenge çözüldü!</div>';
            }
        }
    }
}
