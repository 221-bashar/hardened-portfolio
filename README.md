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
| SQL injection | Prepared statements with bound parameters | `php/contact.php` |
| Cross-site scripting (XSS) | Content-Security-Policy; no user input is ever shown on the site; plain-text responses | nginx, `php/contact.php` |
| Cross-site request forgery (CSRF) | Per-session secret token checked on every form | `php/security.php`, `php/csrf.php` |
| Spam bots | Hidden honeypot field | `php/contact.php` |
| Inbox flooding | nginx rate limit: 5 messages per minute per IP (429 above that), requests capped at 16 KB | nginx |
| Junk or oversized input | Server-side checks: text only, length limits, real email validation | `php/contact.php` |
| Admin-panel attacks (brute force, login bypass) | No login or admin area on the web at all; messages are read over SSH | design decision |
| Session hijacking | `HttpOnly`, `SameSite=Lax`, `Secure` cookies | `php/security.php` |
| Leaked secrets | `config.php` never committed, random DB password on the server, rotated after exposure | `.gitignore`, `php/config.example.php` |
| Database exposure | DB user can only `INSERT` into the `messages` table (cannot read, change or delete); MariaDB listens on 127.0.0.1 only | server |
| Clickjacking, MIME sniffing, downgrade | `X-Frame-Options`, `nosniff`, HSTS, Referrer-Policy, Permissions-Policy | nginx |
| Version fingerprinting | `server_tokens off`, `expose_php = Off` | nginx, PHP |
| Network attack surface | Firewall allows only ports 22, 80, 443; SSH with keys only | DigitalOcean |
| Known vulnerabilities | Automatic security updates | `unattended-upgrades` |

### Why there is no login
An earlier version had a studio login and inbox, kept on purpose as a course exercise in finding vulnerabilities.
I first hardened it (login gate, brute-force lockout, session regeneration), then removed it completely:
a feature that doesn't exist can't be attacked. Messages are read on the server over SSH:

```
mariadb -e "SELECT id, name, email, message, created_at FROM business_card.messages;"
```

## Run it locally
1. Install PHP 8.3 (with `mysqli` and `mbstring`) and MariaDB.
2. Create the database, the `messages` table and a `webapp` user with `INSERT` on that table only.
3. Copy `php/config.example.php` to `php/config.php` and fill in your values.
4. Start the server: `php -S localhost:8000 -t .`

## Next steps
- Redesign with a new layout and a projects section
- Move the honeypot's inline style into CSS so the CSP can drop `'unsafe-inline'`