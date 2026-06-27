<?php
/** API lab paylaşılan yardımcılar (profil durumu + sonuç/solve tespiti). */

if (!function_exists('api_profile_init')) {
    function api_profile_init(): void
    {
        if (!isset($_SESSION['api_profile'])) {
            $_SESSION['api_profile'] = [
                'name'     => current_user()['username'] ?? 'guest',
                'role'     => 'user',
                'is_admin' => 0,
            ];
        }
    }
}

if (!function_exists('api_profile_result')) {
    function api_profile_result(): array
    {
        $p = $_SESSION['api_profile'];
        $elevated = (($p['role'] ?? 'user') !== 'user') || !empty($p['is_admin']);
        if ($elevated) {
            mark_solved('api');
        }
        return ['profile' => $p, 'elevated' => $elevated, '_solved' => $elevated];
    }
}

if (!function_exists('api_lab_ui')) {
    function api_lab_ui(string $hint): void
    {
        api_profile_init();
        echo '<p>Profil güncelleme API\'si: <code>POST ?api=profile</code>. '
           . 'JSON döner. Amaç: profilde yetki yükselt (role≠user ya da is_admin).</p>';
        echo '<pre class="result">Mevcut profil: ' . e(json_encode($_SESSION['api_profile'])) . '</pre>';
        echo '<p style="color:var(--muted);font-size:.85rem">' . $hint . '</p>';
        echo '<form method="post" action="?api=profile" target="_blank">'
           . 'name: <input name="name" value="eve"> '
           . 'rol/alan: <input name="role" value="admin"> '
           . '<button type="submit">POST /api?profile</button></form>';
        echo '<p style="color:var(--muted);font-size:.8rem">curl örn: '
           . '<code>curl -b cookie "URL/vulnerabilities/api/index.php?api=profile" -d "role=admin"</code></p>';
        if (Scoreboard::isSolved('api')) {
            echo '<div class="result">✅ API mass-assignment ile yetki yükseltildi — çözüldü!</div>';
        }
    }
}
