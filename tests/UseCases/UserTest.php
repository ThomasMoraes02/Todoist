<?php 
namespace Todoist\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Application\UseCases\Users\CreateUser;
use Todoist\Application\UseCases\Users\DeleteUser;
use Todoist\Application\UseCases\Users\UpdateUser;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;
use Todoist\Infra\Encoders\EncoderArgon2Id;
use Todoist\Infra\Repositories\Memory\UserRepositoryMemory;

class UserTest extends TestCase
{
    public static UserRepository $userRepository;

    public static Encoder $encoder;

    public static function setUpBeforeClass(): void
    {
        self::$userRepository = new UserRepositoryMemory();
        self::$encoder = new EncoderArgon2Id();
    }

    public function test_create_user(): void
    {
        $input = [
            'name' => 'Thomas Moraes',
            'email' => 'thomas@gmail.com',
            'password' => '123456'
        ];

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $output = $useCase->execute($input);

        $user = self::$userRepository->byUuid($output['uuid']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($input['name'], $user->name);
        $this->assertEquals($input['email'], $user->email);
        $this->assertTrue(self::$encoder->decode($input['password'], $user->password));
    }

    public function test_update_user()
    {
        $inputCreateUser = [
            'name' => 'Thomas Moraes',
            'email' => 'thomas@gmail.com',
            'password' => '123456'
        ];

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $outputCreateUser = $useCase->execute($inputCreateUser);

        $inputUpdateUser = [
            'uuid' => $outputCreateUser['uuid'],
            'name' => 'Thomas Vinicius',
            'email' => 'tho@gmail.com'
        ];

        $useCase = new UpdateUser(self::$userRepository, self::$encoder);
        $outputUpdateUser = $useCase->execute($inputUpdateUser);

        $user = self::$userRepository->byUuid($outputUpdateUser['uuid']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($inputUpdateUser['name'], $user->name);
        $this->assertEquals($inputUpdateUser['email'], $user->email);
        $this->assertTrue(self::$encoder->decode('123456', $user->password));
    }

    public function test_delete_user()
    {
        $inputCreateUser = [
            'name' => 'Thomas Moraes',
            'email' => 'thomas@gmail.com',
            'password' => '123456'
        ];

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $outputCreateUser = $useCase->execute($inputCreateUser);

        $inputDeleteUser = [
            "uuid" => $outputCreateUser["uuid"],
        ];

        $useCase = new DeleteUser(self::$userRepository);
        $outputDeleteUser = $useCase->execute($inputDeleteUser);

        $this->assertEquals("User deleted successfully", $outputDeleteUser["message"]);
    }
}