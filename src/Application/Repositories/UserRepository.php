<?php 
namespace Todoist\Application\Repositories;

use Todoist\Domain\Entities\User;
use Todoist\Domain\ValueObjects\Email;

interface UserRepository
{
    public function byUuid(string $uuid): ?User;

    public function byEmail(Email $email): ?User;

    public function save(User $user): void;
}