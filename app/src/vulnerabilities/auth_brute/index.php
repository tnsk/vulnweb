<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();
vuln_dispatch('auth_brute');
