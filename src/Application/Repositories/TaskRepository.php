<?php 
namespace Todoist\Application\Repositories;

use Todoist\Domain\Entities\Task\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    public function find(string $uuid): ?Task;

    public function update(Task $task): void;

    public function delete(Task $task): void;

    public function findAllByUserUuid(string $uuid): ?array;
}