<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=scraplog;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+08:00'",
    ]);
} catch (PDOException $ex) {
    http_response_code(500);
    exit('Cannot connect to the database. Check that MySQL is running in XAMPP and that sql/schema.sql was imported.');
}
