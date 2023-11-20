<?php 
namespace Todoist\Domain\Factories;

use Todoist\Domain\Entities\Task\Task;

class TaskFactory
{
    public function __construct() {}

    public function create(string $title, string $description, ?string $due_date = null, ?string $userId = null, ?string $parentTaskUuid = null, ?string $priority = null): Task
    {
        return Task::create($title, $description, $due_date, $userId, $parentTaskUuid, $priority);
    }

    public function restore(string $uuid, string $title, string $description, ?string $due_date = null, ?string $userId = null, ?string $parentTaskUuid = null, ?string $priority = null): Task
    {
        return Task::restore($uuid, $title, $description, $due_date, $userId, $parentTaskUuid, $priority);
    }
}