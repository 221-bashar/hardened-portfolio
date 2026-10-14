<?php
require __DIR__ . "/config.php";
require __DIR__ . "/security.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Open the contact form and press Send.";
    exit;
}

if (!post_is_this_site()) {
    http_response_code(403);
    echo "Request rejected (CSRF). Use the form on this site.";
    exit;
}

if (trim($_POST["company"] ?? "") !== "") {
    echo "Thanks. Your message was received.";
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || $message === "") {
    echo "Missing fields.";
    exit;
}

$stmt = $mysqli->prepare(
    "INSERT INTO messages (name, email, message) VALUES (?, ?, ?)"
);
if (!$stmt) {
    echo "Could not store the message.";
    exit;
}
$stmt->bind_param("sss", $name, $email, $message);
if (!$stmt->execute()) {
    echo "Could not store the message.";
    exit;
}
$stmt->close();
$mysqli->close();

echo "Thanks. Your message was stored.";