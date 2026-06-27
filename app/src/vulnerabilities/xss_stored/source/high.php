<?php
/**
 * Stored XSS — HIGH
 * KASITLI BOZUK SAVUNMA: <script> kalıbı regex ile silinir; olay-handler vektörleri kalır.
 * Bypass:  <svg onload=alert(1)>  veya  <img src=x onerror=alert(1)>
 */
$conn = DB::mysqli();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name'] ?? '';
    $comment = $_POST['comment'] ?? '';
    ids_log('xss_stored', $comment);
    $comment = preg_replace('/<\s*\/?\s*script[^>]*>/i', '', $comment);
    $name    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
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
    $html .= '<div class="result"><strong>' . $row['name'] . '</strong>: ' . $row['comment'] . '</div>';
}
echo $html;
if (xss_detect($html, 'xss_stored')) {
    echo '<div class="result">✅ Olay-handler vektörü ile aşıldı — challenge çözüldü!</div>';
}
