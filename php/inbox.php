<?php
require __DIR__ . "/config.php";
require __DIR__ . "/security.php";

if (empty($lab_studio)) {
    http_response_code(404);
    header("Content-Type: text/plain; charset=UTF-8");
    echo "Studio is disabled on this host.";
    exit;
}

require_login();

$result = $mysqli->query(
    "SELECT id, name, email, message FROM messages ORDER BY id"
);
if (!$result) {
    echo "Query failed.";
    exit;
}

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}

header("Content-Type: text/html; charset=UTF-8");
echo "<!doctype html><html lang=\"en\"><head><meta charset=\"UTF-8\"><title>Inbox</title></head><body>";
echo "<p>Logged in as " . e($_SESSION["username"]) . "</p>";
echo "<form method=\"POST\" action=\"/php/logout.php\">";
echo "<input type=\"hidden\" name=\"csrf\" value=\"" . e(csrf_token()) . "\">";
echo "<button type=\"submit\">Log out</button></form>";

echo "<table border=\"1\">";
echo "<tr><th>id</th><th>name</th><th>email</th><th>message</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . e((string) $row["id"]) . "</td>";
    echo "<td>" . e($row["name"]) . "</td>";
    echo "<td>" . e($row["email"]) . "</td>";
    echo "<td>" . e($row["message"]) . "</td>";
    echo "</tr>";
}
echo "</table></body></html>";
$result->free();
$mysqli->close();