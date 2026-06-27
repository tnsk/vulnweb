<?php
/**
 * gadget.php — deserialization lab'ı için POP gadget sınıfı.
 * __wakeup() magic method'u, unserialize sırasında otomatik çalışır ve
 * saldırgan kontrollü $cmd'yi shell'e geçirir (RCE).
 */
if (!class_exists('Pwn')) {
    class Pwn
    {
        public $cmd = '';
        public $result = '';

        public function __wakeup()
        {
            // KASITLI: unserialize edilen nesnenin alanı doğrudan shell'e
            if ($this->cmd !== '') {
                $this->result = (string) shell_exec($this->cmd . ' 2>&1');
            }
        }
    }
}

/** Yardımcı: gadget çıktısını gösterir ve solve tespiti yapar. */
function deser_show_result($obj): void
{
    if ($obj instanceof Pwn && $obj->result !== '') {
        echo '<div class="result"><strong>Gadget __wakeup çıktısı:</strong>' . "\n\n" . e($obj->result) . '</div>';
        if (preg_match('/uid=\d+\(|root:.*:0:0:/', $obj->result)) {
            mark_solved('deserialization');
            echo '<div class="result">✅ PHP Object Injection → RCE — challenge çözüldü!</div>';
        }
    } elseif (is_object($obj)) {
        echo '<div class="result">Nesne oluştu: ' . e(get_class($obj)) . ' (cmd boş)</div>';
    } else {
        echo '<div class="result">Veri: ' . e(var_export($obj, true)) . '</div>';
    }
}

/** Hazır payload üretir (öğretim kolaylığı). */
function deser_sample_payload(string $cmd = 'id'): string
{
    $p = new Pwn();
    $p->cmd = $cmd;
    return serialize($p);   // O:3:"Pwn":2:{s:3:"cmd";...;s:6:"result";s:0:"";}
}
