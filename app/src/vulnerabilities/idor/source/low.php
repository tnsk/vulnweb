<?php
/**
 * IDOR / Broken Access Control — LOW
 * KASITLI ZAFİYET: ?id= ile sipariş çekilir, SAHİPLİK kontrolü yoktur.
 * Sömürü:  ?id=1, ?id=2 ... başkalarının siparişini (gizli notunu) oku.
 */
$uid = (int) ($_SESSION['uid'] ?? 0);
$id  = (int) ($_GET['id'] ?? $uid);

echo '<p>Senin kullanıcı id\'in: <code>' . $uid . '</code>. URL\'deki ?id= değerini değiştirmeyi dene.</p>';
echo '<form method="get">Sipariş id: <input type="text" name="id" value="' . e((string) $id) . '"> <button type="submit">Görüntüle</button></form>';

$pdo = DB::pdo();
$stmt = $pdo->prepare('SELECT id, owner_id, item, amount, secret FROM orders WHERE id = ?');
$stmt->execute([$id]);   // sahiplik kontrolü YOK
$o = $stmt->fetch();

if ($o) {
    ids_log('idor', 'order id=' . $id);
    echo '<div class="result">';
    echo 'Sipariş #' . (int) $o['id'] . ' (owner_id=' . (int) $o['owner_id'] . ")\n";
    echo 'Ürün: ' . e($o['item']) . "\n";
    echo 'Tutar: ' . e((string) $o['amount']) . "\n";
    echo 'Gizli not: ' . e($o['secret']);
    echo '</div>';
    if ((int) $o['owner_id'] !== $uid) {
        mark_solved('idor');
        echo '<div class="result">✅ Başkasının siparişine eriştin (IDOR) — challenge çözüldü!</div>';
    }
}
