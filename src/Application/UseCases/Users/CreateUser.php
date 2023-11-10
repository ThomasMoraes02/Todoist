<?php 
namespace Todoist\Application\UseCases\Users;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;

class CreateUser
{
    public function __construct(private UserRepository $userRepository, private Encoder $encoder) {}

    /**
     * Create User
     *
     * @param array $input
     * @return array
     */
    public function execute(array $input): array
    {
        $user = User::create(
            $input['name'],
            $input['email'],
            $input['password'],
            $this->encoder
        );

        $this->userRepository->save($user);

        $user = $this->userRepository->byEmail($user->email);

        return [
            'uuid' => $user->uuid
        ];
    }
}