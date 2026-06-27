<?php
/**
 * Business Logic / Insecure Design — LOW
 * KASITLI ZAFİYET: miktar doğrulanmaz; NEGATİF miktar negatif tutar üretir → bakiye artar.
 * Sömürü: quantity = -100 girerek bakiyeni başlangıçtan (100) yukarı çıkar.
 */
if (!isset($_SESSION['bl_balance'])) {
    $_SESSION['bl_balance'] = 100.0;
}
$price = 10.0;   // ürün birim fiyatı (sunucu)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = (int) ($_POST['qty'] ?? 0);
    ids_log('business_logic', 'qty=' . $qty);
    // ----- ZAFİYETLİ: qty işareti/aralığı doğrulanmaz -----
    $total = $price * $qty;
    $_SESSION['bl_balance'] -= $total;
    // ------------------------------------------------------
    echo '<div class="result">Sipariş: ' . $qty . ' adet × ' . $price . ' = ' . $total
       . ' · Yeni bakiye: ' . $_SESSION['bl_balance'] . '</div>';
}

echo '<p>Bakiye: <strong>' . $_SESSION['bl_balance'] . '</strong> · Birim fiyat: ' . $price . '</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: miktar negatif olabilir mi?</p>';
echo '<form method="post">Miktar: <input name="qty" value="1"> <button type="submit">Satın al</button></form>';

if ($_SESSION['bl_balance'] > 100.0) {
    mark_solved('business_logic');
    echo '<div class="result">✅ Negatif miktar ile bakiye şişirildi — challenge çözüldü!</div>';
}
