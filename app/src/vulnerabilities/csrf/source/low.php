<?php
/**
 * CSRF — LOW
 * KASITLI ZAFİYET: durum değiştiren işlem (görünen ad güncelle) GET ile, token YOK.
 * (Parola değiştirme ile aynı sınıf; burada auth'u bozmamak için "görünen ad" kullanılır.)
 * Cross-site PoC:  <img src="http://127.0.0.1:8088/vulnerabilities/csrf/source/low.php?name=HACKED">
 */
$uid  = (int) ($_SESSION['uid'] ?? 0);
$name = $_GET['name'] ?? null;

if ($name !== null && $name !== '') {
    ids_log('csrf', $name);
    // ----- ZAFİYETLİ: token kontrolü yok, GET ile state değişimi -----
    $pdo = DB::pdo();
    $pdo->prepare('UPDATE users SET first_name = ? WHERE id = ?')->execute([$name, $uid]);
    // ----------------------------------------------------------------
    mark_solved('csrf');
    echo '<div class="result">✅ Görünen ad <strong>token olmadan</strong> "' . e($name) . '" yapıldı — challenge çözüldü!</div>';
}

$cur = current_user();
echo '<p>Mevcut görünen ad: <strong>' . e($cur['first_name'] ?? '') . '</strong></p>';
echo '<form method="get">Yeni görünen ad: <input type="text" name="name" value=""> <button type="submit">Değiştir</button></form>';

echo '<div class="panel" style="font-size:.85rem"><strong>Cross-site PoC</strong> (başka bir sitede çalışsa kurbanın adını değiştirir):'
   . '<pre>&lt;img src="http://127.0.0.1:8088/vulnerabilities/csrf/source/low.php?name=HACKED"&gt;</pre></div>';
