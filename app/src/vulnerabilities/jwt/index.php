<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();
require_once __DIR__ . '/jwt_lib.php';
vuln_dispatch('jwt');
