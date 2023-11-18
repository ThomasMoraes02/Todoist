<?php 
namespace Todoist\Application\UseCases\Users\CreateUser;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\CreateUser\OutputUser;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;

class CreateUser
{
    public function __construct(private UserRepository $userRepository, private Encoder $encoder) {}

    public function execute(InputUser $input): OutputUser
    {
        $user = User::create(
            $input->name,
            $input->email,
            $input->password,
            $this->encoder
        );

        $this->userRepository->save($user);

        $user = $this->userRepository->byEmail($user->email);

        return new OutputUser(
            $user->uuid
        );
    }
}