<?php 
namespace Todoist\Application\UseCases\Users\UpdateUser;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\UpdateUser\InputUser;
use Todoist\Application\UseCases\Users\UpdateUser\OutputUser;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;

class UpdateUser
{
    public function __construct(private UserRepository $userRepository, private Encoder $encoder) {}

    public function execute(InputUser $input): OutputUser
    {
        $user = $this->userRepository->byUuid($input->uuid);
        if(!$user) return array('error' => 'User not found');

        $user = $user->update(
            $input->name ?? $user->name,
            $input->email ?? $user->email,
            $input->password ?? $user->password,
            $this->encoder
        );

        $this->userRepository->update($user->uuid, $user);

        return new OutputUser(
            $user->uuid,
        );
    }
}