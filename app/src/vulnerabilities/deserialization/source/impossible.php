<?php
/**
 * Deserialization — IMPOSSIBLE (güvenli referans)
 * - Güvenilmeyen veri ASLA unserialize edilmez; json_decode (veri, nesne değil).
 * - İsteğe bağlı: HMAC bütünlük etiketi hash_equals ile doğrulanır.
 */
$data = $_REQUEST['data'] ?? '';

echo '<p>Güvenli: veri JSON olarak çözülür (nesne örneklenmez).</p>';
echo '<form method="post"><input type="text" name="data" size="70" value=""> '
   . 'Örnek: <code>{"cmd":"id"}</code> <button type="submit">Gönder</button></form>';

if ($data !== '') {
    // ----- GÜVENLİ: json_decode (assoc array, nesne değil) -----
    $obj = json_decode($data, true);
    // -----------------------------------------------------------
    if ($obj === null && json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="result">Geçersiz JSON.</div>';
    } else {
        echo '<div class="result">Çözülen veri (yalnızca veri, kod çalışmaz): ' . e(var_export($obj, true)) . '</div>';
    }
}
