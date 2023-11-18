<?php 
namespace Todoist\Application\Repositories;

use Todoist\Domain\Entities\User;
use Todoist\Domain\ValueObjects\Email;

interface UserRepository
{
    public function byUuid(string $uuid): ?User;

    public function byEmail(Email $email): ?User;

    public function save(User $user): void;

    public function update(string $uuid, User $user): void;

    public function delete(string $uuid): void;
}