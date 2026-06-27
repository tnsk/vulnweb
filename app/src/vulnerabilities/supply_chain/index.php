<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();
require_once __DIR__ . '/thirdparty/quicklog/quicklog.php';
require_once __DIR__ . '/thirdparty/safelog/safelog.php';
vuln_dispatch('supply_chain');
