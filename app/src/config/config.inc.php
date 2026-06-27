<?php
/**
 * config.inc.php — aktif config (lab için repo'da bırakıldı; secret'ler sahte).
 * Gerçek projede .gitignore'da olur. Değerler env'den okunur.
 */
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: 'db',
        'name' => getenv('DB_NAME') ?: 'vulndb',
        'user' => getenv('DB_USER') ?: 'vulnuser',
        'pass' => getenv('DB_PASSWORD') ?: 'vulnpass',
    ],
    'default_security'      => 'low',
    'ctf_key'               => getenv('CTF_KEY') ?: 'vulnweb-local-ctf-key',
    'enable_dangerous'      => getenv('ENABLE_DANGEROUS') === '1',
    'allow_public_exposure' => getenv('ALLOW_PUBLIC_EXPOSURE') === '1',
    'enable_logging'        => true,
];
