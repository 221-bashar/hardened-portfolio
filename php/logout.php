<?php
require __DIR__ . "/security.php";

begin_session();
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !csrf_ok()) {
    http_response_code(403);
    echo "Use the Log out button.";
    exit;
}

$_SESSION = [];
$params = session_get_cookie_params();
setcookie(session_name(), "", [
    "expires"  => time() - 3600,
    "path"     => $params["path"],
    "secure"   => $params["secure"],
    "httponly" => $params["httponly"],
    "samesite" => $params["samesite"],
]);
session_destroy();

header("Location: /login.html");
exit;