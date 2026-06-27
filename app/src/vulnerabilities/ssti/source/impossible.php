<?php
/**
 * SSTI — IMPOSSIBLE (güvenli referans)
 * Kullanıcı girdisi ASLA şablon kaynağı olmaz. {{key}} yalnızca allowlist'teki
 * DEĞİŞKENLERLE değiştirilir; ifade asla eval edilmez.
 */
$tpl  = $_REQUEST['tpl'] ?? 'Merhaba {{name}}, rolün {{role}}';
$vars = [
    'name' => current_user()['username'] ?? 'misafir',
    'role' => $_SESSION['role'] ?? 'user',
];

echo '<p>Güvenli: yalnızca <code>{{name}}</code>, <code>{{role}}</code> değişkenleri (eval yok).</p>';
echo '<form method="post"><input type="text" name="tpl" size="50" value="' . e($tpl) . '"> <button type="submit">Render</button></form>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ----- GÜVENLİ: yalnızca veri ikamesi, kod yürütme yok -----
    $rendered = preg_replace_callback('/\{\{(\w+)\}\}/', static function ($m) use ($vars) {
        return htmlspecialchars((string) ($vars[$m[1]] ?? ''), ENT_QUOTES, 'UTF-8');
    }, $tpl);
    // -----------------------------------------------------------
    echo '<div class="result">' . e($rendered) . '</div>';
}
