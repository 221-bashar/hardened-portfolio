<?php
// Run from the terminal only:  php php\create_user.php <username>
if (PHP_SAPI !== "cli") {
    http_response_code(404);
    exit;
}
require __DIR__ . "/config.php";

$username = trim($argv[1] ?? "");
if ($username === "") {
    fwrite(STDERR, "Usage: php php/create_user.php <username>\n");
    exit(1);
}

echo "New password: ";
$line = fgets(STDIN);
$password = rtrim($line === false ? "" : $line, "\r\n");
if (strlen($password) < 12) {
    fwrite(STDERR, "Password must be at least 12 characters.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $mysqli->prepare(
    "INSERT INTO users (username, password_hash) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)"
);
$stmt->bind_param("ss", $username, $hash);
if (!$stmt->execute()) {
    fwrite(STDERR, "Could not save the user.\n");
    exit(1);
}
echo "User saved: $username\n";