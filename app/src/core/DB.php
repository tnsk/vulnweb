<?php
/**
 * DB.php — mysqli (zafiyetli kodun string-concat sink'i) ve PDO (impossible/güvenli
 * prepared statement) bağlantılarını sağlar. İkisi de aynı vulndb'ye bağlanır.
 */
declare(strict_types=1);

final class DB
{
    private static ?mysqli $mysqli = null;
    private static ?PDO $pdo = null;

    /** Zafiyetli laboratuvarların kullandığı mysqli bağlantısı. */
    public static function mysqli(): mysqli
    {
        if (self::$mysqli instanceof mysqli) {
            return self::$mysqli;
        }
        $c = $GLOBALS['config']['db'];
        // Hataları exception yerine klasik (kasıtlı: SQL hata mesajı sızsın)
        mysqli_report(MYSQLI_REPORT_OFF);
        $conn = @mysqli_connect($c['host'], $c['user'], $c['pass'], $c['name']);
        if (!$conn) {
            // Bağlantı yoksa kuruluş bekleniyor olabilir; kısa retry
            for ($i = 0; $i < 10 && !$conn; $i++) {
                usleep(500000);
                $conn = @mysqli_connect($c['host'], $c['user'], $c['pass'], $c['name']);
            }
        }
        if (!$conn) {
            http_response_code(503);
            die('DB baglantisi kurulamadi: ' . mysqli_connect_error());
        }
        mysqli_set_charset($conn, 'utf8mb4');
        self::$mysqli = $conn;
        return $conn;
    }

    /** Güvenli (impossible) seviyelerin kullandığı PDO bağlantısı. */
    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }
        $c = $GLOBALS['config']['db'];
        $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4";
        self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return self::$pdo;
    }

    /** users tablosu yoksa lab.sql'i çalıştırarak şemayı kurar. */
    public static function ensureSchema(): void
    {
        $conn = self::mysqli();
        $res = @mysqli_query($conn, "SHOW TABLES LIKE 'users'");
        if ($res && mysqli_num_rows($res) > 0) {
            return;
        }
        self::runLabSql();
    }

    /** lab.sql'i (drop+create+seed) çalıştırır — kurulum/reset için. */
    public static function runLabSql(): void
    {
        $conn = self::mysqli();
        $sql = file_get_contents(CORE_DIR . '/lab.sql');
        if ($sql === false) {
            die('lab.sql okunamadi');
        }
        // Basit ';' splitter (lab.sql yorum + tek-satır statement içerir)
        $stmts = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));
        foreach ($stmts as $stmt) {
            // Yorum-only satırları atla
            $clean = preg_replace('/^\s*--.*$/m', '', $stmt);
            if (trim($clean) === '') {
                continue;
            }
            @mysqli_query($conn, $stmt);
        }
    }
}
