<?php
/** Scoreboard.php — çözüm tespiti ve ilerleme. */
declare(strict_types=1);

final class Scoreboard
{
    public static function markSolved(string $challengeId): void
    {
        $uid = $_SESSION['uid'] ?? 0;
        if (!$uid) {
            return;
        }
        $flag = flag_for($challengeId);
        $pdo = DB::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO scores (user_id, challenge_id, solved, flag)
             VALUES (?, ?, 1, ?)
             ON DUPLICATE KEY UPDATE solved = 1'
        );
        $stmt->execute([$uid, $challengeId, $flag]);
        // Anlık bildirim için flash
        $_SESSION['flash_solved'] = ['id' => $challengeId, 'flag' => $flag];
    }

    public static function isSolved(string $challengeId): bool
    {
        $uid = $_SESSION['uid'] ?? 0;
        if (!$uid) {
            return false;
        }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT 1 FROM scores WHERE user_id = ? AND challenge_id = ? AND solved = 1');
        $stmt->execute([$uid, $challengeId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function solvedIds(): array
    {
        $uid = $_SESSION['uid'] ?? 0;
        if (!$uid) {
            return [];
        }
        $pdo = DB::pdo();
        $stmt = $pdo->prepare('SELECT challenge_id FROM scores WHERE user_id = ? AND solved = 1');
        $stmt->execute([$uid]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    public static function takeFlash(): ?array
    {
        $f = $_SESSION['flash_solved'] ?? null;
        unset($_SESSION['flash_solved']);
        return $f;
    }
}
