<?php 

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Factory\AppFactory;
use Todoist\Presentation\Controllers\UserController;
use Todoist\Presentation\Middlewares\ErrorMiddleware;
use Todoist\Presentation\Middlewares\OutputMiddleware;

$app = AppFactory::create();

$app->add(ErrorMiddleware::class);
$app->add(OutputMiddleware::class);

$app->get("/", function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello world!");
    return $response; 
});

$app->post("/users", [UserController::class, 'store']);