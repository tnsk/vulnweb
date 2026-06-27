<?php
/**
 * Challenges.php — challenge meta verisini (challenges.json) yükler:
 * id, name, category (OWASP), difficulty(1-6), objective, hints[], mitigationUrl, dangerZone.
 */
declare(strict_types=1);

final class Challenges
{
    private static ?array $all = null;

    public static function all(): array
    {
        if (self::$all === null) {
            $json = file_get_contents(CORE_DIR . '/data/challenges.json');
            self::$all = $json ? (json_decode($json, true) ?: []) : [];
        }
        return self::$all;
    }

    public static function get(string $id): ?array
    {
        foreach (self::all() as $c) {
            if (($c['id'] ?? null) === $id) {
                return $c;
            }
        }
        return null;
    }

    /** Menü için kategoriye göre gruplanmış liste. */
    public static function byCategory(): array
    {
        $out = [];
        foreach (self::all() as $c) {
            $out[$c['category'] ?? 'Diğer'][] = $c;
        }
        return $out;
    }
}
