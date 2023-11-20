<?php 
namespace Todoist\Presentation\Controllers;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Factories\UserFactory;

class UserController
{
    public function __construct(private UserFactory $userFactory, private UserRepository $userRepository) {}

    public function store(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(201);
    }
}