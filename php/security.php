<?php
// security.php: shared helpers for the contact form.

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