<?php
/**
 * XXE — IMPOSSIBLE (güvenli referans)
 * - LIBXML_NOENT KULLANILMAZ (PHP 8 / libxml ≥2.9'da entity substitution zaten kapalı).
 * - LIBXML_NONET ile ağ erişimi kapatılır; DTD yüklenmez.
 * - libxml_set_external_entity_loader(null) ile dış entity loader devre dışı.
 */
$default = "<?xml version=\"1.0\"?>\n<!DOCTYPE r [<!ENTITY xxe SYSTEM \"file:///etc/passwd\">]>\n<r>&xxe;</r>";
$xml = $_POST['xml'] ?? $default;

echo '<p>Güvenli parser: dış entity\'ler çözülmez.</p>';
echo '<form method="post"><textarea name="xml" rows="5" cols="70">' . e($xml) . '</textarea><br><button type="submit">Parse</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    libxml_use_internal_errors(true);
    libxml_set_external_entity_loader(static fn() => null);
    // ----- GÜVENLİ: NOENT yok + NONET; entity substitution kapalı -----
    $doc = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);
    // ------------------------------------------------------------------
    if ($doc === false) {
        echo '<div class="result">XML reddedildi/parse edilemedi (güvenli).</div>';
    } else {
        // &xxe; çözülmediği için içerik boştur
        echo '<div class="result">İçerik: ' . e((string) $doc) . ' (dış entity çözülmedi)</div>';
    }
}
