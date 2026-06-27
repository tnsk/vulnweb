<?php
/**
 * Auth.php — basit oturum. Lab'a uygun şekilde parolalar md5 (zayıf) saklanır.
 * Uygulamaya giriş için kullanılır; ayrıca auth_brute / session labları bunu temel alır.
 */
declare(strict_types=1);

final class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['uid']);
    }

    public static function user(): ?array
    {
        if (empty($_SESSION['uid'])) {
            return null;
        }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT id, username, role, first_name, last_name, avatar FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['uid']]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    /** Uygulama login'i (framework). Prepared statement kullanır — burası kasıtlı zafiyet değil. */
    public static function login(string $username, string $password): bool
    {
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT id, password_md5, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $u = $stmt->fetch();
        if ($u && hash_equals($u['password_md5'], md5($password))) {
            session_regenerate_id(true);
            $_SESSION['uid'] = (int) $u['id'];
            $_SESSION['role'] = $u['role'];
            return true;
        }
        return false;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function isAdmin(): bool
    {
        return ($_SESSION['role'] ?? '') === 'admin';
    }
}
