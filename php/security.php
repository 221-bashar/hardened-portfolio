<?php
// security.php: shared security helpers used by every page.

const SESSION_IDLE_SECONDS = 1800;   // log out after 30 minutes of no activity
const MAX_FAILED_LOGINS    = 5;      // per IP address...
const LOCKOUT_MINUTES      = 15;     // ...within this many minutes

// Start the session with safe cookie flags.
function begin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
    session_set_cookie_params([
        "lifetime" => 0,
        "path"     => "/",
        "secure"   => $https,
        "httponly" => true,
        "samesite" => "Lax",
    ]);
    session_start();
}

// CSRF: give this visitor a secret token.
function csrf_token(): string
{
    begin_session();
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}

// CSRF: does the posted token match the one in the session?
function csrf_ok(): bool
{
    begin_session();
    $sent = $_POST["csrf"] ?? "";
    $real = $_SESSION["csrf"] ?? "";
    return is_string($sent) && $real !== "" && hash_equals($real, $sent);
}

function post_is_this_site(): bool
{
    return csrf_ok();
}

// Gate for protected pages: stop here unless logged in (and not idle too long).
function require_login(): void
{
    begin_session();
    $now  = time();
    $last = $_SESSION["last_seen"] ?? 0;
    if (empty($_SESSION["logged_in"]) || ($now - $last) > SESSION_IDLE_SECONDS) {
        $_SESSION = [];
        header("Location: /login.html");
        exit;
    }
    $_SESSION["last_seen"] = $now;
}

function client_ip(): string
{
    return $_SERVER["REMOTE_ADDR"] ?? "unknown";
}

// Brute-force limit: too many recent failures from this IP?
function too_many_failed_logins(mysqli $db): bool
{
    $ip = client_ip();
    $stmt = $db->prepare(
        "SELECT COUNT(*) FROM login_attempts
         WHERE ip = ? AND attempted_at > (NOW() - INTERVAL " . LOCKOUT_MINUTES . " MINUTE)"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count >= MAX_FAILED_LOGINS;
}

function record_failed_login(mysqli $db): void
{
    $ip = client_ip();
    $stmt = $db->prepare("INSERT INTO login_attempts (ip) VALUES (?)");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}