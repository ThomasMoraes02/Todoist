<?php

use DI\ContainerBuilder;
use Todoist\Domain\Factories\UserFactory;
use Todoist\Infra\Encoders\EncoderArgon2Id;
use Todoist\Infra\Repositories\Mysql\UserRepositoryMysql;

use function DI\create;
use function DI\get;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'PDO' => function(): PDO {

        if($_ENV['DB_DRIVER'] === 'sqlite') {
            if(!file_exists(__DIR__ . "/../database/database.sqlite")) {
                touch(__DIR__ . "/../database/database.sqlite");
            }
        }

        match ($_ENV['DB_DRIVER']) {
            'mysql' => $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']),
            'sqlite' => $pdo = new PDO("sqlite:" . __DIR__ . "/../database/database.sqlite")
        };
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);            
        return $pdo;
    },
    'Encoder' => create(EncoderArgon2Id::class),
    'UserFactory' => create(UserFactory::class)->constructor(get('Encoder')),
    'UserRepository' => create(UserRepositoryMysql::class)->constructor(get('PDO'), get('UserFactory')),
]);
return $containerBuilder->build();