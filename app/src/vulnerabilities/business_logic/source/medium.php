<?php
/**
 * Business Logic — MEDIUM
 * KASITLI BOZUK SAVUNMA: qty > 0 kontrol edilir AMA birim fiyat istemciden (hidden) gelir.
 * Bypass: price alanını 0 veya negatif gönder.
 */
if (!isset($_SESSION['bl_balance'])) {
    $_SESSION['bl_balance'] = 100.0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty   = (int) ($_POST['qty'] ?? 0);
    $price = (float) ($_POST['price'] ?? 10);   // BOZUK: fiyat istemciden
    ids_log('business_logic', "qty=$qty price=$price");
    if ($qty <= 0) {
        echo '<div class="result detected">Miktar pozitif olmalı.</div>';
    } else {
        $total = $price * $qty;
        $_SESSION['bl_balance'] -= $total;
        echo '<div class="result">Tutar: ' . $total . ' · Yeni bakiye: ' . $_SESSION['bl_balance'] . '</div>';
    }
}

echo '<p>Bakiye: <strong>' . $_SESSION['bl_balance'] . '</strong></p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: gizli <code>price</code> alanı istemciden geliyor.</p>';
echo '<form method="post">Miktar: <input name="qty" value="1"> '
   . 'Fiyat (gizli): <input name="price" value="10"> <button type="submit">Satın al</button></form>';

if ($_SESSION['bl_balance'] > 100.0) {
    mark_solved('business_logic');
    echo '<div class="result">✅ İstemci-taraflı fiyat ile aşıldı — çözüldü!</div>';
}
