<?php 
namespace Todoist\Infra\Repositories\Memory;

use Todoist\Application\Repositories\TaskRepository;
use Todoist\Domain\Entities\Task\Task;

class TaskRepositoryMemory implements TaskRepository
{
    public function __construct(private array $tasks = []) {}

    public function save(Task $task): void
    {
        $this->tasks[$task->uuid] = $task;
    }

    public function find(string $uuid): ?Task
    {
        return $this->tasks[$uuid] ?? null;
    }

    public function update(Task $task): void
    {
        $this->tasks[$task->uuid] = $task;
    }

    public function delete(Task $task): void
    {
        unset($this->tasks[$task->uuid]);
    }
}