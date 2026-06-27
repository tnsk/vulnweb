<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();
vuln_dispatch('xss_stored');
