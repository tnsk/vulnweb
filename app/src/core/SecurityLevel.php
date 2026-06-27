<?php
/** SecurityLevel.php — global low/medium/high/impossible seviyesi (session'da). */
declare(strict_types=1);

final class SecurityLevel
{
    public const LEVELS = ['low', 'medium', 'high', 'impossible'];

    public static function init(): void
    {
        if (!isset($_SESSION['security'])) {
            $_SESSION['security'] = $GLOBALS['config']['default_security'] ?? 'low';
        }
    }

    public static function get(): string
    {
        return $_SESSION['security'] ?? 'low';
    }

    public static function set(string $level): bool
    {
        if (!in_array($level, self::LEVELS, true)) {
            return false;
        }
        $_SESSION['security'] = $level;
        return true;
    }

    public static function label(string $level): string
    {
        return match ($level) {
            'low'        => 'Low — savunma yok',
            'medium'     => 'Medium — naif/bypass\'lanabilir savunma',
            'high'       => 'High — güçlü ama bypass\'lanabilir',
            'impossible' => 'Impossible — güvenli referans',
            default      => $level,
        };
    }
}
