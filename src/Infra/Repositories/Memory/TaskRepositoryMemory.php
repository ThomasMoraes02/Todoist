<?php 
namespace Todoist\Infra\Repositories\Memory;

use DateTime;
use DateTimeZone;
use Todoist\Domain\Entities\Task\Task;
use Todoist\Application\Repositories\TaskRepository;

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

    public function findAllByUserUuid(string $uuid): ?array
    {
        return array_filter($this->tasks, fn(Task $task) => $task->userId === $uuid) ?? null;
    }

    public function findTasksThatAreDueSoonByUserUuid(string $uuid): ?array
    {
        $today = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $tasks = $this->findAllByUserUuid($uuid);

        return array_filter($tasks, fn(Task $task) => $today->diff($task->due_date)->days > 0) ?? null;
    }

    public function today(): ?array
    {
        $today = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        return array_filter($this->tasks, fn(Task $task) => $today->diff($task->due_date)->days === 0) ?? null;
    }
}