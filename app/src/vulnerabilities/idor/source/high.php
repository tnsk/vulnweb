<?php
/**
 * IDOR — HIGH
 * KASITLI BOZUK SAVUNMA: sahiplik kontrolü var AMA owner_id istemciden (GET) alınır.
 * Bypass:  ?id=2&owner=2  (saldırgan owner değerini de gönderir)
 */
$uid   = (int) ($_SESSION['uid'] ?? 0);
$id    = (int) ($_GET['id'] ?? $uid);
$owner = (int) ($_GET['owner'] ?? $uid);   // BOZUK: istemciye güven

echo '<form method="get">Sipariş id: <input type="text" name="id" value="' . e((string) $id) . '"> '
   . 'owner: <input type="text" name="owner" value="' . e((string) $owner) . '"> '
   . '<button type="submit">Görüntüle</button></form>';

$pdo = DB::pdo();
// Görünüşte güvenli: id VE owner eşleşmeli — ama owner saldırgandan geliyor.
$stmt = $pdo->prepare('SELECT id, owner_id, item, amount, secret FROM orders WHERE id = ? AND owner_id = ?');
$stmt->execute([$id, $owner]);
$o = $stmt->fetch();

if ($o) {
    ids_log('idor', "order id=$id owner=$owner");
    echo '<div class="result">';
    echo 'Sipariş #' . (int) $o['id'] . ' (owner_id=' . (int) $o['owner_id'] . ")\n";
    echo 'Ürün: ' . e($o['item']) . "\nGizli not: " . e($o['secret']);
    echo '</div>';
    if ((int) $o['owner_id'] !== $uid) {
        mark_solved('idor');
        echo '<div class="result">✅ İstemci-taraflı owner kontrolü aşıldı — challenge çözüldü!</div>';
    }
} else {
    echo '<div class="result">Kayıt yok ya da owner eşleşmedi.</div>';
}
