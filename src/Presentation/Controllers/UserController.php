<?php 
namespace Todoist\Presentation\Controllers;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser\CreateUser;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\LoadUser;
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

    /**
     * Create a new user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
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

    /**
     * Show a user
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        $payload = $args['uuid'];

        $useCase = new LoadUser($this->userRepository, $this->userFactory);
        $output = $useCase->execute($payload);

        $response->getBody()->write(json_encode($output));
        return $response;
    }
}