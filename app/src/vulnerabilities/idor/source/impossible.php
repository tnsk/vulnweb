<?php
/**
 * IDOR — IMPOSSIBLE (güvenli referans)
 * Server-side object-level authorization: sorgu OTURUMDAKİ kullanıcıya scope'lanır.
 * owner_id istemciden DEĞİL, $_SESSION'dan gelir.
 */
$uid = (int) ($_SESSION['uid'] ?? 0);
$id  = (int) ($_GET['id'] ?? 0);

echo '<form method="get">Sipariş id: <input type="text" name="id" value=""> <button type="submit">Görüntüle</button></form>';

if ($id > 0) {
    $pdo = DB::pdo();
    // ----- GÜVENLİ: owner_id sunucu tarafı oturumdan -----
    $stmt = $pdo->prepare('SELECT id, item, amount, secret FROM orders WHERE id = ? AND owner_id = ?');
    $stmt->execute([$id, $uid]);
    // -----------------------------------------------------
    $o = $stmt->fetch();
    if ($o) {
        echo '<div class="result">Sipariş #' . (int) $o['id'] . ' — ' . e($o['item'])
           . ' — ' . e((string) $o['amount']) . '</div>';
    } else {
        echo '<div class="result">Bu siparişe erişim yetkin yok ya da sipariş bulunamadı.</div>';
    }
}
