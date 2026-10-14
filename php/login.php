<?php
require __DIR__ . "/config.php";
require __DIR__ . "/security.php";

if (empty($lab_studio)) {
    http_response_code(404);
    header("Content-Type: text/plain; charset=UTF-8");
    echo "Studio is disabled on this host.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /login.html");
    exit;
}

header("Content-Type: text/plain; charset=UTF-8");

if (!post_is_this_site()) {
    http_response_code(403);
    echo "Request rejected (CSRF). Use the login form on this site.";
    exit;
}

if (too_many_failed_logins($mysqli)) {
    http_response_code(429);
    echo "Too many failed attempts. Try again in " . LOCKOUT_MINUTES . " minutes.";
    exit;
}

$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";
if (!is_string($username) || !is_string($password)) {
    http_response_code(400);
    echo "Bad request.";
    exit;
}
$username = trim($username);

if ($username === "" || $password === "") {
    echo "Missing username or password.";
    exit;
}

$stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($hash);
$found = $stmt->fetch();
$stmt->close();

if (!$found || !password_verify($password, $hash)) {
    record_failed_login($mysqli);
    echo "Invalid username or password.";
    exit;
}

session_regenerate_id(true);
$_SESSION["logged_in"] = true;
$_SESSION["username"]  = $username;
$_SESSION["last_seen"] = time();

header("Location: /php/inbox.php");
exit;