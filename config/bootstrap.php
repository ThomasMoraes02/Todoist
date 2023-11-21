<?php

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require_once __DIR__ . "/../vendor/autoload.php";

$dotEnv = Dotenv::createImmutable(__DIR__ . "/../");
$dotEnv->load();

$container = require __DIR__ . "/container.php";

AppFactory::setContainer($container);