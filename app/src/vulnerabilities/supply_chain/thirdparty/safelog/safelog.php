<?php
/**
 * SafeLog 3.0.0 — denetlenmiş, sabitlenmiş (pinned) temiz loglama kütüphanesi.
 * Backdoor yok; integrity hash ile doğrulanmış sürüm (impossible seviyesi bunu kullanır).
 */

function safelog_version(): string
{
    return '3.0.0';
}

function safelog_write(string $msg): string
{
    // Yalnızca loglar; hiçbir koşulda kod çalıştırmaz.
    return 'logged: ' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
}
