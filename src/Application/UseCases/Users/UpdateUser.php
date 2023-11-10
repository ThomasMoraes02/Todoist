<?php 
namespace Todoist\Application\UseCases\Users;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;

class UpdateUser
{
    public function __construct(private UserRepository $userRepository, private Encoder $encoder) {}

    public function execute(array $input): array
    {
        $user = $this->userRepository->byUuid($input["uuid"]);
        if(!$user) return array('error' => 'User not found');

        $user = User::create(
            $input['name'] ?? $user->name,
            $input['email'] ?? $user->email,
            $input['password'] ?? $user->password,
            $this->encoder
        );

        $this->userRepository->update($user->uuid, $user);

        return [
            "uuid" => $user->uuid
        ];
    }
}