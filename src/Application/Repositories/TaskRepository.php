<?php 
namespace Todoist\Application\Repositories;

use Todoist\Domain\Entities\Task\Task;

interface TaskRepository
{
    public function save(Task $task): void;

    public function find(string $uuid): ?Task;
}