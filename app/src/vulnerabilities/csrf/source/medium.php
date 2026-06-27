<?php
/**
 * CSRF — MEDIUM
 * KASITLI BOZUK SAVUNMA: tek savunma Referer header'ının site adını içermesi.
 * Bypass: Referer'ı hiç göndermeme ya da içinde "127.0.0.1" geçen bir değerle spoof etme
 *         (ör. saldırgan sayfası path'inde host adını barındırır).
 */
$uid  = (int) ($_SESSION['uid'] ?? 0);
$name = $_GET['name'] ?? null;

if ($name !== null && $name !== '') {
    ids_log('csrf', $name);
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $host    = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
    // BOZUK: substring Referer kontrolü
    if ($referer !== '' && stripos($referer, $host) !== false) {
        $pdo = DB::pdo();
        $pdo->prepare('UPDATE users SET first_name = ? WHERE id = ?')->execute([$name, $uid]);
        mark_solved('csrf');
        echo '<div class="result">✅ Referer kontrolü aşıldı — "' . e($name) . '" yapıldı — çözüldü!</div>';
    } else {
        echo '<div class="result detected">Reddedildi: Referer kontrolü (' . e($referer ?: 'boş') . ').</div>';
    }
}

$cur = current_user();
echo '<p>Mevcut görünen ad: <strong>' . e($cur['first_name'] ?? '') . '</strong></p>';
echo '<form method="get">Yeni görünen ad: <input type="text" name="name" value=""> <button type="submit">Değiştir</button></form>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: Referer header\'ı kontrolden geçirilebilir/atlanabilir.</p>';
