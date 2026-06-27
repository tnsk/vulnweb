<?php
/**
 * Open Redirect — LOW
 * KASITLI ZAFİYET: next parametresi doğrulanmadan yönlendirmede kullanılır.
 * Sömürü:  ?next=https://evil.example.com   (phishing/OAuth token hırsızlığı)
 * (Lab UI bozulmasın diye gerçek 302 atılmaz; hedef gösterilir + tıklanabilir.)
 */
$next = $_GET['next'] ?? '';

echo '<p>Çıkış linki (next ile geri dönüş): '
   . '<a href="?next=/index.php">meşru</a> · deneyin <code>?next=https://evil.example.com</code></p>';
echo '<form method="get">next: <input type="text" name="next" size="50" value="' . e($next) . '"> <button type="submit">Git</button></form>';

if ($next !== '') {
    ids_log('open_redirect', $next);
    // ----- ZAFİYETLİ: ham hedef -----
    echo '<div class="result">Yönlendirilecek hedef: ' . e($next)
       . "\n" . '<a href="' . e($next) . '">Devam et »</a></div>';
    // --------------------------------
    if (openredirect_external($next)) {
        mark_solved('open_redirect');
        echo '<div class="result">✅ Harici siteye açık yönlendirme — challenge çözüldü!</div>';
    }
}
