<?php 
namespace Todoist\Application\UseCases\Users\UpdateUser;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\UpdateUser\InputUser;
use Todoist\Application\UseCases\Users\UpdateUser\OutputUser;
use Todoist\Domain\Factories\UserFactory;

class UpdateUser
{
    public function __construct(private UserRepository $userRepository, private UserFactory $userFactory) {}

    public function execute(InputUser $input): OutputUser
    {
        $user = $this->userRepository->byUuid($input->uuid);
        if(!$user) return array('error' => 'User not found');

        $user = $this->userFactory->restore(
            $user->uuid,
            $input->name ?? $user->name,
            $input->email ?? $user->email,
            $input->password ?? $user->password
        );

        $this->userRepository->update($user->uuid, $user);

        return new OutputUser(
            $user->uuid,
        );
    }
}