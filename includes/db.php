<?php
require_once __DIR__ . '/../config.php';

$mysqli = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if (!$mysqli) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($mysqli, 'utf8mb4');
?>
