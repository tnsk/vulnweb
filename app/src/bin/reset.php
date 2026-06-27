<?php
/**
 * bin/reset.php — CLI seed-reset. Container içinde:
 *   docker compose exec app php bin/reset.php
 * CI tarafından her test sınıfından önce çağrılabilir (deterministik durum).
 */
$_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
require_once dirname(__DIR__) . '/core/bootstrap.php';

DB::runLabSql();

// Yüklenen dosyaları temizle
foreach (glob(APP_ROOT . '/hackable/uploads/*') ?: [] as $f) {
    if (is_file($f) && !in_array(basename($f), ['.gitkeep', '.gitignore'], true)) {
        @unlink($f);
    }
}

fwrite(STDOUT, "VulnWeb: lab veritabanı sıfırlandı ve seed yüklendi; uploads temizlendi.\n");
