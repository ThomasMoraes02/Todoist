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

        try {
            $input = new InputUser(
                $payload['name'],
                $payload['email'],
                $payload['password']
            );

            $useCase = new CreateUser($this->userRepository, $this->userFactory);
            $output = $useCase->execute($input);
        } catch(InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8')->withStatus(400);
        }

        $response->getBody()->write(json_encode([
            'uuid' => $output->uuid
        ]));
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withHeader("Cache-Control", "no-cache")
        ->withHeader("Cache-Control", "max-age=0")
        ->withHeader("Cache-Control", "must-revalidate")
        ->withHeader("Cache-Control", "proxy-revalidate")
        ->withStatus(201);
    }
}