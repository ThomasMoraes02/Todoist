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
        $pdo = new PDO("sqlite:memory:");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },
    'Encoder' => create(EncoderArgon2Id::class),
    'UserFactory' => create(UserFactory::class)->constructor(get('Encoder')),
    'UserRepository' => create(UserRepositoryMysql::class)->constructor(get('PDO'), get('UserFactory')),
]);
return $containerBuilder->build();