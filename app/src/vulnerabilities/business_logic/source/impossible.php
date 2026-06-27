<?php
/**
 * Business Logic — IMPOSSIBLE (güvenli referans)
 * - Miktar pozitif tam sayı (sunucu doğrulaması)
 * - Birim fiyat sunucuda sabit; istemciden alınmaz
 * - Kupon en fazla bir kez; indirim [0,1] aralığına clamp; tutar >= 0
 */
if (!isset($_SESSION['bl_balance'])) {
    $_SESSION['bl_balance'] = 100.0;
}
$price = 10.0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qty = filter_var($_POST['qty'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100]]);
    if ($qty === false) {
        echo '<div class="result">Geçersiz miktar (1–100 tam sayı).</div>';
    } else {
        $discount = ($_POST['coupon'] ?? '') === 'SAVE50' ? 0.5 : 0.0;   // tek sefer, sabit
        $total = max(0.0, $price * $qty * (1 - $discount));             // negatif olamaz
        if ($total > $_SESSION['bl_balance']) {
            echo '<div class="result">Yetersiz bakiye.</div>';
        } else {
            $_SESSION['bl_balance'] -= $total;
            echo '<div class="result ok">Tutar: ' . $total . ' · Bakiye: ' . $_SESSION['bl_balance'] . '</div>';
        }
    }
}

echo '<p>Bakiye: <strong>' . $_SESSION['bl_balance'] . '</strong></p>';
echo '<form method="post">Miktar: <input name="qty" value="1"> '
   . 'Kupon: <input name="coupon" value=""> <button type="submit">Satın al</button></form>';
