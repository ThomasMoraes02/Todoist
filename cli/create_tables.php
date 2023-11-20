<?php 

$databasePath = require __DIR__ . "/../database/database.sqlite";

try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $pdo->exec('CREATE TABLE IF NOT EXISTS users (uuid TEXT, name TEXT, email TEXT, password LONGTEXT)');

    $pdo->commit();

} catch(PDOException | Throwable $e) {
    $pdo->rollBack();
    echo $e->getMessage();
}