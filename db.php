<?php
// db.php - Database connection configuration

$host = 'mysql.railway.internal';
$dbname = 'railway';
$username = 'root';
$password = 'cmgjtemPqwnyvCkWrXERmIoScrZikNwC';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
