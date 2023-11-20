<?php

use Slim\Factory\AppFactory;

require_once __DIR__ . "/../vendor/autoload.php";
$container = require __DIR__ . "/container.php";

AppFactory::setContainer($container);
$app = AppFactory::create();