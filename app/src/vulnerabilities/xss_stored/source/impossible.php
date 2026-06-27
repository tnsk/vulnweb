<?php
/**
 * Stored XSS — IMPOSSIBLE (güvenli referans)
 * - Depolama: prepared statement
 * - Çıktı: htmlspecialchars(ENT_QUOTES|ENT_HTML5) ile context-aware encoding
 */
$pdo = DB::pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim((string) ($_POST['name'] ?? ''));
    $comment = trim((string) ($_POST['comment'] ?? ''));
    $stmt = $pdo->prepare('INSERT INTO guestbook (name, comment) VALUES (?, ?)');
    $stmt->execute([$name, $comment]);
}

echo '<form method="post">';
echo '  İsim: <input type="text" name="name" value=""><br>';
echo '  Yorum:<br><textarea name="comment"></textarea><br>';
echo '  <button type="submit">Gönder</button>';
echo '</form>';

echo '<h3>Ziyaretçi Defteri</h3>';
$stmt = $pdo->query('SELECT name, comment FROM guestbook ORDER BY id DESC');
foreach ($stmt->fetchAll() as $row) {
    // ----- GÜVENLİ: çıktıda encoding -----
    echo '<div class="result"><strong>'
       . htmlspecialchars($row['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
       . '</strong>: '
       . htmlspecialchars($row['comment'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
       . '</div>';
}
