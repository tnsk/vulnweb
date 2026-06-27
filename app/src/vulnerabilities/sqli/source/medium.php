<?php
/**
 * SQL Injection — MEDIUM
 * KASITLI BOZUK SAVUNMA: mysqli_real_escape_string KULLANILIR ama değer tırnaksız
 * (numeric context) → escape işe yaramaz. Ayrıca POST'a taşınarak "gizlendi".
 * Payload örn (POST id):  1 UNION SELECT username, password_md5 FROM users-- -
 */
$id = $_POST['id'] ?? null;

echo '<p>User ID seç/gir (POST):</p>';
echo '<form method="post">';
echo '  <input type="text" name="id" value="' . e((string) $id) . '" placeholder="1"> ';
echo '  <button type="submit">Sorgula</button>';
echo '</form>';

if ($id !== null && $id !== '') {
    ids_log('sqli', (string) $id);
    $conn = DB::mysqli();

    // ----- BOZUK SAVUNMA: escape var ama tırnak yok (numeric context) -----
    $clean = mysqli_real_escape_string($conn, (string) $id);
    $sql = "SELECT first_name, last_name FROM users WHERE id = $clean";
    // ---------------------------------------------------------------------

    echo '<div class="result"><strong>Çalışan sorgu:</strong> ' . e($sql) . "\n";
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        echo "\n<span class=\"detected\">SQL hatası:</span> " . e(mysqli_error($conn));
    } else {
        $solved = false;
        while ($row = mysqli_fetch_assoc($res)) {
            echo "\nFirst name: {$row['first_name']} &nbsp; Surname: {$row['last_name']}";
            foreach ($row as $v) {
                if (is_string($v) && preg_match('/^[a-f0-9]{32}$/', $v)) {
                    $solved = true;
                }
            }
        }
        if ($solved) {
            mark_solved('sqli');
            echo "\n\n✅ <strong>md5 hash sızdırıldı — challenge çözüldü!</strong>";
        }
    }
    echo '</div>';
}
