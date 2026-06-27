<?php
require_once __DIR__ . '/core/bootstrap.php';
Auth::logout();
header('Location: /login.php');
exit;
