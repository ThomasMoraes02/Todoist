<?php 
namespace Todoist\Application\UseCases\Users\CreateUser;

use InvalidArgumentException;
use Todoist\Domain\ValueObjects\Email;
use Todoist\Domain\Factories\UserFactory;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\CreateUser\OutputUser;

class CreateUser
{
    public function __construct(private UserRepository $userRepository, private UserFactory $userFactory) {}

    public function execute(InputUser $input): OutputUser
    {
        $userExists = $this->userRepository->byEmail(new Email($input->email));

        if($userExists) {
            die();
        }

        if($userExists) throw new InvalidArgumentException('User already exists');

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