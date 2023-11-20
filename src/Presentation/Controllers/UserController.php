<?php 
namespace Todoist\Presentation\Controllers;

use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Factories\UserFactory;

class UserController
{
    private UserRepository $userRepository;

    private UserFactory $userFactory;

    public function __construct(ContainerInterface $container) 
    {
        $this->userFactory = $container->get('UserFactory');
        $this->userRepository = $container->get('UserRepository');
    }

    public function store(Request $request, Response $response, array $args): Response
    {
        return $response->withStatus(201);
    }
}