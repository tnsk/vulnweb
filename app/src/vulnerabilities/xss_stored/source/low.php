<?php
/**
 * Stored XSS — LOW
 * KASITLI ZAFİYET: yorum DB'ye kaydedilir ve her ziyaretçide ham (encode'suz) basılır.
 * Payload (yorum):  <script>alert(document.domain)</script>
 */
$conn = DB::mysqli();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'] ?? '';
    $comment = $_POST['comment'] ?? '';
    ids_log('xss_stored', $comment);
    // Depolama için SQL escape (XSS kasıtlı; SQLi bu lab'ın konusu değil)
    $n = mysqli_real_escape_string($conn, $name);
    $c = mysqli_real_escape_string($conn, $comment);
    mysqli_query($conn, "INSERT INTO guestbook (name, comment) VALUES ('$n', '$c')");
}

echo '<form method="post">';
echo '  İsim: <input type="text" name="name" value=""><br>';
echo '  Yorum:<br><textarea name="comment"></textarea><br>';
echo '  <button type="submit">Gönder</button>';
echo '</form>';

echo '<h3>Ziyaretçi Defteri</h3>';
$res = mysqli_query($conn, 'SELECT name, comment FROM guestbook ORDER BY id DESC');
$html = '';
while ($row = mysqli_fetch_assoc($res)) {
    // ----- ZAFİYETLİ: ham çıktı -----
    $html .= '<div class="result"><strong>' . $row['name'] . '</strong>: ' . $row['comment'] . '</div>';
    // --------------------------------
}
echo $html;
if (xss_detect($html, 'xss_stored')) {
    echo '<div class="result">✅ Kalıcı XSS yerleşti — challenge çözüldü!</div>';
}
