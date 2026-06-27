<?php
/**
 * SQL Injection — LOW
 * KASITLI ZAFİYET: kullanıcı girdisi sorguya doğrudan (string concat) eklenir.
 * Payload örn:  1' UNION SELECT username, password_md5 FROM users-- -
 */
$id = $_GET['id'] ?? null;

echo '<p>Bir User ID gir (geçerli: 1–5):</p>';
echo '<form method="get">';
echo '  <input type="text" name="id" value="' . e((string) $id) . '" placeholder="1"> ';
echo '  <button type="submit">Sorgula</button>';
echo '</form>';

if ($id !== null && $id !== '') {
    ids_log('sqli', (string) $id);
    $conn = DB::mysqli();

    // ----- ZAFİYETLİ SATIR -----
    $sql = "SELECT first_name, last_name FROM users WHERE id = '$id'";
    // ---------------------------

    echo '<div class="result"><strong>Çalışan sorgu:</strong> ' . e($sql) . "\n";
    $res = mysqli_query($conn, $sql);
    if (!$res) {
        echo "\n<span class=\"detected\">SQL hatası:</span> " . e(mysqli_error($conn));
    } else {
        $solved = false;
        while ($row = mysqli_fetch_assoc($res)) {
            echo "\nID: {$id} &nbsp; First name: {$row['first_name']} &nbsp; Surname: {$row['last_name']}";
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
