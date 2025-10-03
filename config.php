<?php
// config.php
$DB_HOST = 'localhost';
$DB_NAME = 'invoice';
$DB_USER = 'invoice';
$DB_PASS = 'TLJEjAbYpintfSf7';

// PDO
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage());
}
