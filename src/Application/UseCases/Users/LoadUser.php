<?php 
namespace Todoist\Application\UseCases\Users;

use InvalidArgumentException;
use Todoist\Domain\Factories\UserFactory;
use Todoist\Application\Repositories\UserRepository;

class LoadUser
{
    public function __construct(private UserRepository $userRepository, private UserFactory $userFactory) {}

    public function execute(string $uuid): array
    {
        $user = $this->userRepository->byUuid($uuid);

        if(!$user) throw new InvalidArgumentException('User not found');

        return [
            'uuid' => $user->uuid,
            'name' => $user->name,
            'email' => $user->email->__toString(),
            'password' => $user->password
        ];
    }
}