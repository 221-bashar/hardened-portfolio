<?php
require __DIR__ . "/security.php";

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");
echo json_encode(["csrf" => csrf_token()]);