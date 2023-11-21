<?php 
namespace Todoist\Presentation\Controllers;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser\CreateUser;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
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
        $payload = json_decode($request->getBody()->getContents(), true);

        $input = new InputUser(
            $payload['name'],
            $payload['email'],
            $payload['password']
        );

        $useCase = new CreateUser($this->userRepository, $this->userFactory);
        $output = $useCase->execute($input);

        $response->getBody()->write(json_encode([
            'uuid' => $output->uuid
        ]));

        return $response->withStatus(201);
    }
}