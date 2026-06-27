<?php
require_once __DIR__ . '/core/bootstrap.php';

$err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Auth::login((string) ($_POST['username'] ?? ''), (string) ($_POST['password'] ?? ''))) {
        header('Location: /index.php');
        exit;
    }
    $err = 'Geçersiz kullanıcı adı veya parola.';
}
if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}
?>
<!doctype html>
<html lang="tr"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Sign in — VulnWeb</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<div class="caution"><span class="dot"></span> <b>INTENTIONALLY VULNERABLE</b> — localhost only · do not expose to any network</div>
<div class="auth-wrap">
  <div class="login-box">
    <div class="brand"><span class="glyph">V</span> VulnWeb <span class="sub">sec-lab</span></div>
    <div class="tag">OWASP Top 10:2025 training lab</div>
    <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="text" name="username" placeholder="username" value="admin" required>
      <input type="password" name="password" placeholder="password" required>
      <button type="submit">Sign in</button>
    </form>
    <div class="hint">default · <span style="color:var(--accent-2)">admin</span> / <span style="color:var(--accent-2)">password</span></div>
  </div>
</div>
</body></html>
