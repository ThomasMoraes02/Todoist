<?php 

try {
    $pdo = new PDO('sqlite:memory:');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $pdo->exec('CREATE TABLE IF NOT EXISTS users (uuid TEXT, name TEXT, email TEXT, password LONGTEXT)');

    $pdo->commit();

} catch(PDOException | Throwable $e) {
    $pdo->rollBack();
    echo $e->getMessage();
}