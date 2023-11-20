<?php 
namespace Todoist\Application\UseCases\Users\CreateUser;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\CreateUser\OutputUser;
use Todoist\Domain\Factories\UserFactory;

class CreateUser
{
    public function __construct(private UserRepository $userRepository, private UserFactory $userFactory) {}

    public function execute(InputUser $input): OutputUser
    {
        $user = $this->userFactory->create(
            $input->name,
            $input->email,
            $input->password
        );

        $this->userRepository->save($user);

        $user = $this->userRepository->byEmail($user->email);

        return new OutputUser(
            $user->uuid
        );
    }
}