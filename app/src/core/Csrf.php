<?php
/**
 * Csrf.php — gerçek anti-CSRF token sistemi. KASITLI olarak yalnızca
 * 'impossible' seviyelerde kullanılır; düşük seviyeler CSRF'e açıktır.
 */
declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="user_token" value="' . e(self::token()) . '">';
    }

    /** Sabit-zamanlı doğrulama. */
    public static function check(?string $token): bool
    {
        return is_string($token)
            && !empty($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /** İmpossible seviyelerde çağrılır; başarısızsa durur. */
    public static function require(): void
    {
        if (!self::check($_REQUEST['user_token'] ?? null)) {
            die('CSRF token doğrulaması başarısız.');
        }
    }
}
