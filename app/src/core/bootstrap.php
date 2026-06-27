<?php
/**
 * bootstrap.php — her sayfanın ilk satırı. Config + DB + helper'ları yükler,
 * session başlatır, public-exposure guardrail'ini çalıştırır, DB'yi otomatik kurar.
 */
declare(strict_types=1);

if (defined('VULNWEB_BOOTSTRAPPED')) {
    return;
}
define('VULNWEB_BOOTSTRAPPED', true);

// Lab kökü (docroot)
define('APP_ROOT', dirname(__DIR__));            // /var/www/html
define('CORE_DIR', __DIR__);                     // /var/www/html/core

// Hatalar açık (kasıtlı — bilgi sızdırma labı); ini zaten display_errors=On.
error_reporting(E_ALL);

// Config
$GLOBALS['config'] = require APP_ROOT . '/config/config.inc.php';

// Çekirdek sınıf/fonksiyonlar
require_once CORE_DIR . '/functions.php';
require_once CORE_DIR . '/DB.php';
require_once CORE_DIR . '/Guard.php';
require_once CORE_DIR . '/SecurityLevel.php';
require_once CORE_DIR . '/Csrf.php';
require_once CORE_DIR . '/Auth.php';
require_once CORE_DIR . '/Challenges.php';
require_once CORE_DIR . '/Scoreboard.php';
require_once CORE_DIR . '/Logger.php';
require_once CORE_DIR . '/layout.php';

// Session (zayıf cookie param'ları php-insecure.ini'den gelir)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('VULNWEBSESSID');
    session_start();
}

// Güvenlik guardrail'i: loopback dışı bind / public IP ise dur.
Guard::enforce();

// Varsayılan güvenlik seviyesi
SecurityLevel::init();

// DB tabloları yoksa otomatik kur (DVWA "first run" davranışı)
DB::ensureSchema();
