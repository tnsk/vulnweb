[English](README.md) · **Türkçe**

# VulnWeb

Web güvenliği öğrenmek için bilerek açık bırakılmış PHP uygulaması. OWASP Top 10:2025'i kapsayan 23 lab.

Her lab 4 seviyeli: `low`, `medium`, `high`, `impossible`. `impossible` güvenli sürüm. Olay zaten bozuk seviyeleri onunla diff'leyip farkı görmek.

PHP 8.4 + MariaDB 11.8, Docker'da çalışıyor. Kötü bir fix ile düzgün bir fix'i tek yerde karşılaştırmak istediğim için yaptım. Fikirleri DVWA, bWAPP, Mutillidae ve Juice Shop'tan aldım.

> **Bilerek açık.** Gerçek, sömürülebilir bug'lar var (SQLi, RCE, dosya yükleme, SSRF, dahası). Sadece localhost, izole makine. Asla ağa açma, asla gerçek veri girme.

## Çalıştırma

```bash
docker compose up -d --build
```

<http://127.0.0.1:8088> aç, `admin` / `password` ile gir.

Adminer (DB arayüzü) <http://127.0.0.1:8089> adresinde. DB host'a açılmıyor. 8088/8089 portları malum 3306/3307/8081 çakışmalarından kaçmak için.

Her şey 127.0.0.1'e bağlı. Uygulamaya yerel olmayan bir adresten erişilirse başlamayı reddediyor, `ALLOW_PUBLIC_EXPOSURE=1` set etmediğin sürece (etme).

## Nasıl çalışıyor

Bir lab seç, üst bardan seviyeyi ayarla. `low`'da hiç savunma yok. `medium` yaygın ama bypass edilebilen fix, klasik tuzak. `high` daha sağlam ama gene de geçilir. `impossible` ise gerçekten kullanacağın sürüm.

Her labda "kaynağı gör" ve yan yana diff (açık vs güvenli) var. Çözümler sunucu tarafında tespit edilip scoreboard'da tutuluyor. Gönderdiğin payload bir IDS/exploit log panelinde görünüyor, biraz blue-team tarafı isteyen için.

## Lablar

OWASP 2025'in on kategorisinde 23 lab. `DZ` = danger zone (gerçek RCE, dosya yazma veya iç ağ erişimi).

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
| A07 Authentication Failures | Brute-force / zayıf login | CWE-307 |
| A07 | Session Fixation | CWE-384 |
| A07 | JWT (alg:none, zayıf secret) | CWE-347 |
| A08 Integrity Failures | Insecure Deserialization `DZ` | CWE-502 |
| A08 | XXE `DZ` | CWE-611 |
| A09 Logging & Alerting | Log Injection | CWE-117 |
| A10 Mishandling Exceptional Conditions | Fail-Open | CWE-636 |

## Sıfırlama

```bash
docker compose exec app php bin/reset.php
# ya da komple sil
docker compose down -v && docker compose up -d
```

Uygulama içinde Reset sayfası da var.

## Testler

```bash
BASE=http://127.0.0.1:8088 bash tests/smoke_test.sh
```

43 kontrol: her açık `low`'da tetikleniyor, `impossible`'da bloklanıyor.

## Lisans

MIT, [LICENSE](LICENSE)'a bak. Sadece eğitim için.
