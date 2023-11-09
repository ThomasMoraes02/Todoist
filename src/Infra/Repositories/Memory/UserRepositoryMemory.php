<?php 
namespace Todoist\Infra\Repositories\Memory;

use Todoist\Application\Repositories\UserRepository;
use Todoist\Domain\Entities\User;
use Todoist\Domain\ValueObjects\Email;

class UserRepositoryMemory implements UserRepository
{
    public function __construct(private array $users = []) {}

    public function byUuid(string $uuid): ?User
    {
        return $this->users[$uuid] ?? null;
    }

    public function byEmail(Email $email): ?User
    {
        $user = array_filter($this->users, fn($user) => $user->email === $email) ?? null;
        return !empty($user) ? current($user) : null;
    }

    public function save(User $user): void
    {
        $this->users[$user->uuid] = $user;
    }
}