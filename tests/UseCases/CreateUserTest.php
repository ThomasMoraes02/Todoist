<?php 
namespace Todoist\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;
use Todoist\Infra\Encoders\EncoderArgon2Id;
use Todoist\Infra\Repositories\Memory\UserRepositoryMemory;

class CreateUserTest extends TestCase
{
    public function test_create_user(): void
    {
        $userRepository = new UserRepositoryMemory();
        $encoder = new EncoderArgon2Id();

        $input = [
            'name' => 'Thomas Moraes',
            'email' => 'thomas@gmail.com',
            'password' => '123456'
        ];

        $useCase = new CreateUser($userRepository, $encoder);
        $output = $useCase->execute($input);

        $user = $userRepository->byUuid($output['uuid']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($input['name'], $user->name);
        $this->assertEquals($input['email'], $user->email);
        $this->assertTrue($encoder->decode($input['password'], $user->password));
    }
}