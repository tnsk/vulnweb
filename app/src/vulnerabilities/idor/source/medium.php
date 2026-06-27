<?php
/**
 * IDOR — MEDIUM
 * KASITLI BOZUK SAVUNMA: id base64 ile "gizlenir" (security by obscurity).
 * Bypass:  echo -n 2 | base64  ->  Mg==  ;  ?ref=Mg==
 */
$uid = (int) ($_SESSION['uid'] ?? 0);
$ref = $_GET['ref'] ?? base64_encode((string) $uid);
$id  = (int) base64_decode((string) $ref, true);

echo '<p>Siparişler artık opak <code>ref</code> ile gösteriliyor (görünüşte güvenli).</p>';
echo '<form method="get">ref: <input type="text" name="ref" value="' . e((string) $ref) . '"> <button type="submit">Görüntüle</button></form>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: ref sadece base64\'lenmiş id. <code>Mg==</code> = 2.</p>';

$pdo = DB::pdo();
$stmt = $pdo->prepare('SELECT id, owner_id, item, amount, secret FROM orders WHERE id = ?');
$stmt->execute([$id]);
$o = $stmt->fetch();

if ($o) {
    ids_log('idor', 'order ref=' . $ref);
    echo '<div class="result">';
    echo 'Sipariş #' . (int) $o['id'] . ' (owner_id=' . (int) $o['owner_id'] . ")\n";
    echo 'Ürün: ' . e($o['item']) . "\nGizli not: " . e($o['secret']);
    echo '</div>';
    if ((int) $o['owner_id'] !== $uid) {
        mark_solved('idor');
        echo '<div class="result">✅ Obscurity aşıldı (IDOR) — challenge çözüldü!</div>';
    }
}
