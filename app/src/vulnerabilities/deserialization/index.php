<?php
require_once __DIR__ . '/../../core/bootstrap.php';
require_login();
require_once __DIR__ . '/gadget.php';   // Pwn gadget sınıfı unserialize'dan ÖNCE tanımlı olmalı
vuln_dispatch('deserialization');
