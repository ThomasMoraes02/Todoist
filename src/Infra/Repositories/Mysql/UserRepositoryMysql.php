<?php 
namespace Todoist\Infra\Repositories\Mysql;

use PDO;
use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;
use Todoist\Domain\Factories\UserFactory;
use Todoist\Domain\ValueObjects\Email;

class UserRepositoryMysql implements UserRepository
{
    public function __construct(private PDO $pdo, private UserFactory $userFactory) {}

    public function byUuid(string $uuid): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE uuid = :uuid');
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return $this->userFactory->restore(
            $row['uuid'],
            $row['name'],
            $row['email'],
            $row['password']
        );
    }

    public function byEmail(Email $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return $this->userFactory->restore(
            $row['uuid'],
            $row['name'],
            $row['email'],
            $row['password']
        );
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO users (uuid, name, email, password) VALUES (:uuid, :name, :email, :password)');
        $stmt->bindValue(':uuid', $user->uuid, PDO::PARAM_STR);
        $stmt->bindValue(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->password, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function update(string $uuid, User $user): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, email = :email, password = :password WHERE uuid = :uuid');
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindValue(':name', $user->name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $user->password, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function delete(string $uuid): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE uuid = :uuid');
        $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
    }
}