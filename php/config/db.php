<?php
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '']);

if ($isLocal) {
    $DB_HOST = 'localhost';
    $DB_PORT = '3306';
    $DB_NAME = 'reusechic';
    $DB_USER = 'root';
    $DB_PASS = '';
} else {
    $DB_HOST = 'reusechic-db-f7fqi0';
    $DB_PORT = '3306';
    $DB_NAME = 'reusechic';
    $DB_USER = 'reusechic';
    $DB_PASS = '47beLVrSzV8Hkba5sscj';
}

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER, $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Erro de conexão: ' . htmlspecialchars($e->getMessage()));
}
