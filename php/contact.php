<?php
require __DIR__ . "/config.php";
require __DIR__ . "/security.php";

const MAX_NAME    = 100;
const MAX_EMAIL   = 254;   // the longest valid email address
const MAX_MESSAGE = 5000;

header("Content-Type: text/plain; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Open the contact form and press Send.";
    exit;
}

if (!post_is_this_site()) {
    http_response_code(403);
    echo "Request rejected (CSRF). Use the form on this site.";
    exit;
}

// Honeypot: humans never see this field, bots fill it.
if (trim((string) ($_POST["company"] ?? "")) !== "") {
    echo "Thanks. Your message was received.";
    exit;
}

$name    = $_POST["name"] ?? "";
$email   = $_POST["email"] ?? "";
$message = $_POST["message"] ?? "";

// Reject arrays and other tricks: every field must be plain text.
if (!is_string($name) || !is_string($email) || !is_string($message)) {
    http_response_code(400);
    echo "Bad request.";
    exit;
}

$name    = trim($name);
$email   = trim($email);
$message = trim($message);

if ($name === "" || $email === "" || $message === "") {
    http_response_code(400);
    echo "Missing fields.";
    exit;
}

if (mb_strlen($name) > MAX_NAME || mb_strlen($email) > MAX_EMAIL || mb_strlen($message) > MAX_MESSAGE) {
    http_response_code(400);
    echo "Too long. Name max " . MAX_NAME . ", message max " . MAX_MESSAGE . " characters.";
    exit;
}

if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    http_response_code(400);
    echo "Please enter a valid email address.";
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
if (!$stmt) {
    http_response_code(500);
    echo "Could not store the message.";
    exit;
}
$stmt->bind_param("sss", $name, $email, $message);
if (!$stmt->execute()) {
    http_response_code(500);
    echo "Could not store the message.";
    exit;
}
$stmt->close();
$mysqli->close();

echo "Thanks. Your message was stored.";