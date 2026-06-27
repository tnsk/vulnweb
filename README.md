**English** · [Türkçe](README.tr.md)

# VulnWeb

A deliberately vulnerable PHP app for learning web security. 23 labs covering the OWASP Top 10:2025.

Each lab has 4 levels: `low`, `medium`, `high`, `impossible`. `impossible` is the secure version. The whole point is diffing the broken levels against it.

PHP 8.4 + MariaDB 11.8, runs in Docker. I built it because I kept wanting to compare a bad fix against a good one in one place. Took ideas from DVWA, bWAPP, Mutillidae and Juice Shop.

> **Intentionally insecure.** Real, exploitable bugs (SQLi, RCE, file upload, SSRF, more). Localhost only, isolated machine. Never put it on a network, never enter real data.

## Run

```bash
docker compose up -d --build
```

Open <http://127.0.0.1:8088>, log in `admin` / `password`.

Adminer (DB UI) is at <http://127.0.0.1:8089>. The DB isn't published to the host. Ports 8088/8089 to dodge the usual 3306/3307/8081 clashes.

Everything binds to 127.0.0.1. If the app is reached from a non-local address it refuses to start, unless you set `ALLOW_PUBLIC_EXPOSURE=1` (don't).

## How it works

Pick a lab, set severity in the top bar. `low` has no defences. `medium` is the common fix that's bypassable, the classic trap. `high` is stronger but still beatable. `impossible` is the version you'd actually ship.

There's a "view source" and a side-by-side diff (vulnerable vs secure) on every lab. Solves are detected server-side and tracked on a scoreboard. Your payload shows up in an IDS/exploit log panel if you want some blue-team side too.

## Labs

23 labs across all ten OWASP 2025 categories. `DZ` = danger zone (real RCE, file writes, or internal network access).

| OWASP 2025 | Lab | CWE |
|---|---|---|
| A01 Broken Access Control | IDOR | CWE-639 |
| A01 | CSRF | CWE-352 |
| A01 | SSRF `DZ` | CWE-918 |
| A01 | Open Redirect | CWE-601 |
| A01 | API Mass Assignment | CWE-915 |
| A02 Security Misconfiguration | Misconfiguration | CWE-16 |
| A03 Software Supply Chain | Backdoored Dependency `DZ` | CWE-1357 |
| A04 Cryptographic Failures | Weak Crypto | CWE-327 |
| A05 Injection | SQL Injection (UNION + blind) | CWE-89 |
| A05 | XSS (reflected) | CWE-79 |
| A05 | XSS (stored) | CWE-79 |
| A05 | Command Injection `DZ` | CWE-78 |
| A05 | LFI / RFI `DZ` | CWE-98 |
| A05 | File Upload `DZ` | CWE-434 |
| A05 | SSTI `DZ` | CWE-1336 |
| A06 Insecure Design | Business Logic | CWE-840 |
| A07 Authentication Failures | Brute-force / weak login | CWE-307 |
| A07 | Session Fixation | CWE-384 |
| A07 | JWT (alg:none, weak secret) | CWE-347 |
| A08 Integrity Failures | Insecure Deserialization `DZ` | CWE-502 |
| A08 | XXE `DZ` | CWE-611 |
| A09 Logging & Alerting | Log Injection | CWE-117 |
| A10 Mishandling Exceptional Conditions | Fail-Open | CWE-636 |

## Reset

```bash
docker compose exec app php bin/reset.php
# or nuke it
docker compose down -v && docker compose up -d
```

There's also a Reset page in the app.

## Tests

```bash
BASE=http://127.0.0.1:8088 bash tests/smoke_test.sh
```

43 checks: every vuln fires at `low`, blocked at `impossible`.

## License

MIT, see [LICENSE](LICENSE). Education only.
