<?php 
namespace Todoist\Test\Integration;

use PDO;
use PHPUnit\Framework\TestCase;
use Todoist\Domain\Entities\Encoder;
use Todoist\Infra\Encoders\EncoderArgon2Id;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Infra\Repositories\Mysql\UserRepositoryMysql;
use Todoist\Application\UseCases\Users\CreateUser\InputUser;
use Todoist\Application\UseCases\Users\CreateUser\CreateUser;
use Todoist\Application\UseCases\Users\UpdateUser\UpdateUser;
use Todoist\Application\UseCases\Users\UpdateUser\InputUser as UpdateUserInputUser;
use Todoist\Domain\Factories\UserFactory;

class UserRepositoryMysqlTest extends TestCase
{
    private static PDO $pdo;

    private static UserRepository $userRepository;

    private static Encoder $encoder;

    private static UserFactory $userFactory;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$pdo->exec('CREATE TABLE IF NOT EXISTS users (uuid TEXT, name TEXT, email TEXT, password TEXT)');

        self::$encoder = new EncoderArgon2Id();
        self::$userFactory = new UserFactory(self::$encoder);
        self::$userRepository = new UserRepositoryMysql(self::$pdo, self::$userFactory);
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    public function test_must_save_and_update_user_in_mysql_repository()
    {
        $useCase = new CreateUser(self::$userRepository, self::$userFactory);

        $inputCreateUser = new InputUser(
            'Thomas Moraes',
            'thom@gmail.com',
            '123456',
        );

        $output = $useCase->execute($inputCreateUser);

        $useCase = new UpdateUser(self::$userRepository, self::$userFactory);

        $inputUpdateUser = new UpdateUserInputUser(
            $output->uuid,
            'Thomas Vinicius de Moraes',
            'thomas@gmail.com',
        );

        $output = $useCase->execute($inputUpdateUser);

        $user = self::$userRepository->byUuid($output->uuid);

        $this->assertEquals('Thomas Vinicius de Moraes', $user->name);
        $this->assertEquals('thomas@gmail.com', $user->email);
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}