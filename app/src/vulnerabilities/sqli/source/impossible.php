<?php
/**
 * SQL Injection — IMPOSSIBLE (güvenli referans)
 * - PDO prepared statement (bind, parametre)
 * - is_numeric / intval doğrulaması
 * - anti-CSRF token
 * - least-privilege beklentisi (DB user salt yetkili)
 */
echo '<p>User ID gir (güvenli implementasyon):</p>';
echo '<form method="post">';
echo '  <input type="text" name="id" value="" placeholder="1"> ';
echo Csrf::field();
echo '  <button type="submit">Sorgula</button>';
echo '</form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::require();
    $id = $_POST['id'] ?? '';

    if (!is_numeric($id)) {
        echo '<div class="result">Geçersiz girdi: yalnızca sayısal ID kabul edilir.</div>';
    } else {
        $pdo = DB::pdo();
        // ----- GÜVENLİ: parameterized query -----
        $stmt = $pdo->prepare('SELECT first_name, last_name FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        // ----------------------------------------
        $row = $stmt->fetch();
        echo '<div class="result">';
        if ($row) {
            echo 'First name: ' . e($row['first_name']) . ' &nbsp; Surname: ' . e($row['last_name']);
        } else {
            echo 'Kayıt bulunamadı.';
        }
        echo '</div>';
    }
}
