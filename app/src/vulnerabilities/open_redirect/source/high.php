<?php
/**
 * Open Redirect — HIGH
 * KASITLI BOZUK SAVUNMA: parse_url ile host bizim host'umuz mu kontrol edilir.
 * Bypass: backslash hilesi —  /\evil.example.com  (parse_url host'u null görür ama
 *         tarayıcı '\' -> '/' yapıp //evil.example.com olarak harici gider).
 */
$next = $_GET['next'] ?? '';

echo '<p>"Savunma": <code>parse_url</code> ile host doğrulanır.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>/\\evil.example.com</code> (parse_url ile tarayıcı farklı yorumlar).</p>';
echo '<form method="get">next: <input type="text" name="next" size="50" value="' . e($next) . '"> <button type="submit">Git</button></form>';

if ($next !== '') {
    ids_log('open_redirect', $next);
    $host = parse_url($next, PHP_URL_HOST);
    $self = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';
    // BOZUK: parse_url backslash'i host olarak görmez -> kontrol atlanır
    if ($host !== null && strcasecmp($host, $self) !== 0) {
        echo '<div class="result detected">Reddedildi: harici host (' . e($host) . ').</div>';
    } else {
        echo '<div class="result">Hedef: ' . e($next) . ' <a href="' . e($next) . '">Devam »</a></div>';
        if (openredirect_external($next)) {
            mark_solved('open_redirect');
            echo '<div class="result">✅ Backslash hilesiyle aşıldı — challenge çözüldü!</div>';
        }
    }
}
