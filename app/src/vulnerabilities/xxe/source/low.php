<?php
/**
 * XXE — LOW
 * KASITLI ZAFİYET: LIBXML_NOENT | LIBXML_DTDLOAD ile dış entity'ler çözülür.
 * Payload:
 *   <?xml version="1.0"?>
 *   <!DOCTYPE r [<!ENTITY xxe SYSTEM "file:///etc/passwd">]>
 *   <r>&xxe;</r>
 */
$default = "<?xml version=\"1.0\"?>\n<!DOCTYPE r [<!ENTITY xxe SYSTEM \"file:///etc/passwd\">]>\n<r>&xxe;</r>";
$xml = $_POST['xml'] ?? $default;

echo '<p>Aşağıdaki XML sunucuda parse edilir (dış entity çözümü AÇIK):</p>';
echo '<form method="post"><textarea name="xml" rows="5" cols="70">' . e($xml) . '</textarea><br><button type="submit">Parse</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ids_log('xxe', $xml);
    libxml_use_internal_errors(true);
    // ----- ZAFİYETLİ: entity substitution açık -----
    $doc = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOENT | LIBXML_DTDLOAD);
    // -----------------------------------------------
    if ($doc === false) {
        echo '<div class="result detected">XML parse hatası.</div>';
    } else {
        $content = (string) $doc;
        echo '<div class="result">' . e($content) . '</div>';
        if (preg_match('/root:.*:0:0:/', $content)) {
            mark_solved('xxe');
            echo '<div class="result">✅ XXE ile dosya okundu — challenge çözüldü!</div>';
        }
    }
}
