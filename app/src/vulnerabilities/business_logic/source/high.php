<?php
/**
 * Business Logic — HIGH
 * KASITLI BOZUK SAVUNMA: qty>0 ve fiyat sunucuda AMA kupon STACK'lenebilir (her POST'ta
 * %50 indirim birikir) → indirim >%100 olunca tutar negatife döner (para iade).
 * Bypass: aynı kuponu (SAVE50) birden çok kez uygula.
 */
if (!isset($_SESSION['bl_balance'])) {
    $_SESSION['bl_balance'] = 100.0;
}
if (!isset($_SESSION['bl_discount'])) {
    $_SESSION['bl_discount'] = 0.0;
}
$price = 10.0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty    = max(1, (int) ($_POST['qty'] ?? 1));
    $coupon = $_POST['coupon'] ?? '';
    ids_log('business_logic', "qty=$qty coupon=$coupon");
    if ($coupon === 'SAVE50') {
        $_SESSION['bl_discount'] += 0.5;   // BOZUK: tekrar tekrar birikir
    }
    $total = $price * $qty * (1 - $_SESSION['bl_discount']);   // indirim>1 -> negatif
    $_SESSION['bl_balance'] -= $total;
    echo '<div class="result">İndirim: %' . ($_SESSION['bl_discount'] * 100)
       . ' · Tutar: ' . $total . ' · Yeni bakiye: ' . $_SESSION['bl_balance'] . '</div>';
}

echo '<p>Bakiye: <strong>' . $_SESSION['bl_balance'] . '</strong> · Toplam indirim: %'
   . ($_SESSION['bl_discount'] * 100) . '</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>SAVE50</code> kuponunu defalarca uygula.</p>';
echo '<form method="post">Miktar: <input name="qty" value="1"> '
   . 'Kupon: <input name="coupon" value="SAVE50"> <button type="submit">Satın al</button></form>';

if ($_SESSION['bl_balance'] > 100.0) {
    mark_solved('business_logic');
    echo '<div class="result">✅ Kupon stacking ile bakiye şişti — çözüldü!</div>';
}
