<?php
// Copy this file to config.php and fill in the real values.
// config.php is listed in .gitignore and must never be committed.
mysqli_report(MYSQLI_REPORT_OFF);

$db_host = "127.0.0.1";
$db_user = "webapp";
$db_pass = "CHANGE_ME";
$db_name = "business_card";
$db_port = 3306;

// true only on your own computer. On any public server it must be false.
$lab_studio = false;

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}
$mysqli->set_charset("utf8mb4");