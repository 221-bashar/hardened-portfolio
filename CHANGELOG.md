# Changelog

## 2026-10-22
- PHP2Shell (CVE-2026-109618/109619): confirmed php8.3 8.3.6-0ubuntu0.24.04.13 installed by unattended-upgrades, PHP-FPM restarted
- Disabled session.upload_progress (/etc/php/8.3/fpm/conf.d/99-hardening.ini)
- Compromise checks clean: no memory-limit fatals, no unexpected files, processes or cron
- Fixed web root permissions: files 666 -> 644 root:root, config.php 640 root:www-data
- bashar sudo password reset via the DigitalOcean Recovery Console; root password locked

## 2026-10-15
- Removed the studio login and inbox (course exercise); messages are read over SSH
- Database user reduced to INSERT on `messages` only; dropped `users` and `login_attempts` tables
- Contact form: server-side length limits and email validation (needs `php8.3-mbstring`)
- nginx: rate limit on the contact form (5/min per IP, burst 3) and a 16 KB request cap
- Strict CSP: honeypot style moved to CSS, `'unsafe-inline'` and `data:` removed
- SSH: new `bashar` user with sudo; root login disabled; keys only
- Server config copied into `deploy/`
- `.gitignore`: block credential files

## 2026-10-14
- Login hardened (gate, lockout, session regeneration), later removed
- Security headers, `expose_php = Off`, security updates and reboot
- Database password rotated after it appeared in a chat
- Repository created on GitHub

## 2026-10-12
- Droplet created; nginx, PHP 8.3, MariaDB installed; firewall 22/80/443
- Site deployed; HTTPS with Let's Encrypt for basharkhatib.me
- Database password rotated as a precaution after the PHP2Shell exposure window