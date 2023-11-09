<?php 
namespace Todoist\Application\UseCases\Users;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\UseCase;

class DeleteUser implements UseCase
{
    public function __construct(private UserRepository $userRepository) {}

    public function execute(array $input): array
    {
        $user = $this->userRepository->byUuid($input['uuid']);
        if (!$user) return array("error" => "User not found");

        $this->userRepository->delete($user->uuid);

        return [
            'message' => 'User deleted successfully'
        ];
    }
}