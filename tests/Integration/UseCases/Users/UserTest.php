<?php 
namespace Todoist\Test\Integration\UseCases\Users;

use PHPUnit\Framework\TestCase;
use Todoist\Domain\Entities\User;
use Todoist\Domain\Entities\Encoder;
use Todoist\Infra\Encoders\EncoderArgon2Id;
use Todoist\Application\UseCases\Users\DeleteUser;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Infra\Repositories\Memory\UserRepositoryMemory;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\CreateUser\CreateUser;
use Todoist\Application\UseCases\Users\UpdateUser\UpdateUser;
use Todoist\Application\UseCases\Users\UpdateUser\InputUser as UpdateUserInputUser;

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
        $inputCreateUser = new InputUser(
            'Thomas Moraes',
            'thomas@gmail.com',
            '123456'
        );

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $output = $useCase->execute($inputCreateUser);

        $user = self::$userRepository->byUuid($output->uuid);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($inputCreateUser->name, $user->name);
        $this->assertEquals($inputCreateUser->email, $user->email);
        $this->assertTrue(self::$encoder->decode($inputCreateUser->password, $user->password));
    }

    public function test_update_user()
    {
        $inputCreateUser = new InputUser(
            'Thomas Moraes',
            'thomas@gmail.com',
            '123456'
        );

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $outputCreateUser = $useCase->execute($inputCreateUser);

        $inputUpdateUser = new UpdateUserInputUser(
            $outputCreateUser->uuid,
            'Thomas Vinicius',
            'tho@gmail.com'
        );

        $useCase = new UpdateUser(self::$userRepository, self::$encoder);
        $outputUpdateUser = $useCase->execute($inputUpdateUser);

        $user = self::$userRepository->byUuid($outputUpdateUser->uuid);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($inputUpdateUser->name, $user->name);
        $this->assertEquals($inputUpdateUser->email, $user->email);
        $this->assertTrue(self::$encoder->decode('123456', $user->password));
    }

    public function test_delete_user()
    {
        $inputCreateUser = new InputUser(
            'Thomas Moraes',
            'thomas@gmail.com',
            '123456'
        );

        $useCase = new CreateUser(self::$userRepository, self::$encoder);
        $outputCreateUser = $useCase->execute($inputCreateUser);

        $inputDeleteUser = [
            "uuid" => $outputCreateUser->uuid,
        ];

        $useCase = new DeleteUser(self::$userRepository);
        $outputDeleteUser = $useCase->execute($inputDeleteUser);

        $this->assertEquals("User deleted successfully", $outputDeleteUser["message"]);
    }
}