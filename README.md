# basharkhatib.me

My portfolio site, built during a web fundamentals course and hardened for the public internet.
Live at **https://basharkhatib.me**

## Stack
- HTML5, CSS, vanilla JavaScript
- PHP 8.3 (PHP-FPM) + MariaDB
- nginx on Ubuntu 24.04 (DigitalOcean droplet), HTTPS via Let's Encrypt

## Security
| Threat | Protection | Where |
|---|---|---|
| SQL injection | Prepared statements with bound parameters | `php/contact.php`, `php/login.php` |
| Cross-site scripting (XSS) | Output escaping with `htmlspecialchars`, plus a Content-Security-Policy | `php/inbox.php`, nginx |
| Cross-site request forgery (CSRF) | Per-session secret token checked on every form | `php/security.php`, `php/csrf.php` |
| Spam bots | Hidden honeypot field | `php/contact.php` |
| Skipping the login | `require_login()` gate on every protected page | `php/security.php`, `php/inbox.php` |
| Password guessing | Lockout after 5 failed logins per IP in 15 minutes | `php/security.php`, `php/login.php` |
| Session hijacking / fixation | `HttpOnly`, `SameSite=Lax`, `Secure` cookies, new session ID at login, 30-minute idle timeout | `php/security.php` |
| Stolen passwords | Only bcrypt hashes stored (`password_hash`) | `php/create_user.php` |
| Account creation by visitors | User script runs from the terminal only | `php/create_user.php` |
| Leaked secrets | `config.php` never committed, random DB password on the server | `.gitignore`, `php/config.example.php` |
| Database exposure | DB user limited to `SELECT, INSERT`; MariaDB listens on 127.0.0.1 only | server |
| Private area on the public server | Studio switched off by a per-machine flag (fails closed) | `php/config.php` |
| Clickjacking, MIME sniffing, downgrade | `X-Frame-Options`, `nosniff`, HSTS, Referrer-Policy, Permissions-Policy | nginx |
| Version fingerprinting | `server_tokens off`, `expose_php = Off` | nginx, PHP |
| Network attack surface | Firewall allows only ports 22, 80, 443; SSH with keys only | DigitalOcean |
| Known vulnerabilities | Automatic security updates | `unattended-upgrades` |

## Run it locally
1. Install PHP 8.3 (with `mysqli`) and MariaDB.
2. Create the database and tables, and a `webapp` user.
3. Copy `php/config.example.php` to `php/config.php`, fill in your values, and set `$lab_studio = true`.
4. Create your login: `php php/create_user.php <username>`
5. Start the server: `php -S localhost:8000 -t .`

## Next steps
- Redesign with a new layout and a projects section
- Move the honeypot's inline style into CSS so the CSP can drop `'unsafe-inline'`