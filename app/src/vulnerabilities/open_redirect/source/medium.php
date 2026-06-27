<?php
/**
 * Open Redirect — MEDIUM
 * KASITLI BOZUK SAVUNMA: "'/' ile başlamalı" kontrolü.
 * Bypass:  //evil.example.com  (protocol-relative; '/' ile başlar ama harici)
 */
$next = $_GET['next'] ?? '';

echo '<p>"Savunma": hedef <code>/</code> ile başlamalı.</p>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: <code>//evil.example.com</code> de "/" ile başlar.</p>';
echo '<form method="get">next: <input type="text" name="next" size="50" value="' . e($next) . '"> <button type="submit">Git</button></form>';

if ($next !== '') {
    ids_log('open_redirect', $next);
    if ($next[0] !== '/') {
        echo '<div class="result detected">Reddedildi: göreli yol olmalı.</div>';
    } else {
        echo '<div class="result">Hedef: ' . e($next) . ' <a href="' . e($next) . '">Devam »</a></div>';
        if (openredirect_external($next)) {
            mark_solved('open_redirect');
            echo '<div class="result">✅ Protocol-relative ile aşıldı — challenge çözüldü!</div>';
        }
    }
}
