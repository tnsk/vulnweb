<?php
/**
 * Security Misconfiguration — HIGH
 * KASITLI BOZUK SAVUNMA: debug paneli yok AMA verbose hata yönetimi (display_errors +
 * istisna trace'inin ekrana basılması) iç yolları/SQL'i sızdırır.
 * Sömürü:  ?q=gecersiz_tablo   (PDOException trace'i sızar)
 */
echo '<p>Tablo görüntüleyici (yalnızca geçerli tablo adı ver):</p>';
echo '<form method="get">tablo: <input type="text" name="q" value="users"> <button type="submit">Göster</button></form>';
echo '<p style="color:var(--muted);font-size:.85rem">İpucu: geçersiz bir tablo adı ver de hata yönetimine bak.</p>';

if (isset($_GET['q'])) {
    ids_log('misconfig', 'q=' . $_GET['q']);
    try {
        $stmt = DB::pdo()->query('SELECT * FROM ' . $_GET['q'] . ' LIMIT 1');
        $row = $stmt->fetch();
        echo '<div class="result">' . e(var_export($row, true)) . '</div>';
    } catch (\Throwable $ex) {
        // KASITLI: verbose hata — mesaj + stack trace ekrana
        echo '<div class="result detected"><strong>Hata (verbose):</strong>' . "\n";
        echo e($ex->getMessage()) . "\n\n" . e($ex->getTraceAsString());
        echo '</div>';
        if (strpos($ex->getTraceAsString(), '/var/www/html') !== false) {
            mark_solved('misconfig');
            echo '<div class="result">✅ Verbose hata yönetimi iç yolları sızdırdı — çözüldü!</div>';
        }
    }
}
