<?php 

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Todoist\Presentation\Controllers\UserController;

$app->get("/", function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello world!");
    return $response; 
});

$app->post("/users", [UserController::class, 'store']);