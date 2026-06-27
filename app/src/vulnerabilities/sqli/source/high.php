<?php
/**
 * SQL Injection — HIGH
 * KASITLI BOZUK SAVUNMA: LIMIT 1 + case-SENSITIVE keyword blocklist (sadece küçük harf).
 * Bypass: anahtar kelimeleri büyük/karışık harfle yaz.
 * Payload örn:  1' UnIoN SeLeCt username, password_md5 FROM users-- -
 */
$id = $_GET['id'] ?? null;

echo '<p>User ID gir (LIMIT 1 + filtre uygulanır):</p>';
echo '<form method="get">';
echo '  <input type="text" name="id" value="' . e((string) $id) . '" placeholder="1"> ';
echo '  <button type="submit">Sorgula</button>';
echo '</form>';

if ($id !== null && $id !== '') {
    ids_log('sqli', (string) $id);
    $conn = DB::mysqli();

    // ----- BOZUK SAVUNMA: case-sensitive blocklist (yalnızca küçük harf) + LIMIT 1 -----
    $filtered = str_replace(['union', 'select', 'from', 'where'], '', (string) $id);
    // not: yalnızca küçük harf kalıpları silinir -> "UnIoN SeLeCt" bypass eder.
    $sql = "SELECT first_name, last_name FROM users WHERE id = '$filtered' LIMIT 1";
    // ----------------------------------------------------------------------------------

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
