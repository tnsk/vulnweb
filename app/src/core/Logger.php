<?php
/**
 * Logger.php — basit kural-tabanlı IDS / exploit log (blue-team modülü).
 * Payload'da saldırı imzası ararsa 'detected' işaretler ve DB + dosyaya yazar.
 */
declare(strict_types=1);

final class Logger
{
    private const SIGNATURES = [
        '/union\s+select/i',
        '/<script/i',
        '/onerror\s*=/i',
        '/(\.\.\/){2,}/',
        '/;\s*(cat|ls|id|whoami|nc|curl|wget)\b/i',
        '/\bsleep\s*\(/i',
        '/php:\/\//i',
        '/system\s*\(/i',
        '/\b(etc\/passwd)\b/i',
    ];

    public static function logAttempt(string $challengeId, string $payload): bool
    {
        if (!($GLOBALS['config']['enable_logging'] ?? true)) {
            return false;
        }
        $detected = false;
        foreach (self::SIGNATURES as $re) {
            if (preg_match($re, $payload)) {
                $detected = true;
                break;
            }
        }
        try {
            $pdo = DB::pdo();
            $stmt = $pdo->prepare(
                'INSERT INTO exploit_logs (ip, challenge_id, payload, detected) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([client_ip(), $challengeId, mb_substr($payload, 0, 2000), $detected ? 1 : 0]);
        } catch (\Throwable $e) {
            // sessiz
        }
        return $detected;
    }

    public static function recent(int $limit = 100): array
    {
        try {
            $pdo = DB::pdo();
            $stmt = $pdo->query('SELECT ts, ip, challenge_id, payload, detected FROM exploit_logs ORDER BY id DESC LIMIT ' . (int) $limit);
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
