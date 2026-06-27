<?php
/**
 * Guard.php — güvenlik guardrail'i. Uygulama loopback dışında bir host'tan
 * erişiliyor (yani muhtemelen ağa/internete açılmış) gibiyse ve
 * ALLOW_PUBLIC_EXPOSURE=1 değilse, sert bir uyarı ile durur.
 */
declare(strict_types=1);

final class Guard
{
    public static function enforce(): void
    {
        if ($GLOBALS['config']['allow_public_exposure'] ?? false) {
            return; // bilinçli opt-in
        }

        $host = $_SERVER['SERVER_ADDR'] ?? '';
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';

        // İzin verilenler: loopback ve docker bridge'in tipik özel aralıkları
        // (container'ın kendi IP'si özeldir; asıl tehlike public/routable erişimdir).
        $clientOk = self::isPrivateOrLoopback($remote);

        if (!$clientOk) {
            self::block($remote);
        }
    }

    private static function isPrivateOrLoopback(string $ip): bool
    {
        if ($ip === '' || $ip === 'unknown') {
            return true; // CLI / belirsiz -> engelleme
        }
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        }
        // Özel/rezerve aralıklar (LAN/docker) public sayılmaz ama yine de izinli:
        // public (routable) IP'lerden gelen istek = tehlike.
        $isPublic = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
        return $isPublic === false; // private/reserved ise true (izinli)
    }

    private static function block(string $ip): void
    {
        http_response_code(403);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="tr"><meta charset="utf-8"><title>VulnWeb — ENGELLENDİ</title>';
        echo '<body style="font-family:system-ui;background:#1a0000;color:#ffdddd;padding:3rem;max-width:800px;margin:auto">';
        echo '<h1>⛔ Erişim engellendi</h1>';
        echo '<p><strong>VulnWeb KASITLI olarak zafiyetli bir eğitim uygulamasıdır.</strong> ';
        echo 'Public/routable bir adresten (' . e($ip) . ') erişiliyor — bu, uygulamanın ';
        echo 'ağa veya internete açılmış olabileceği anlamına gelir. Bu çok tehlikelidir.</p>';
        echo '<p>Yalnızca <code>127.0.0.1</code> üzerinden, izole bir makinede kullan. ';
        echo 'Gerçekten ne yaptığını biliyorsan <code>ALLOW_PUBLIC_EXPOSURE=1</code> ile başlat.</p>';
        echo '</body></html>';
        exit;
    }
}
