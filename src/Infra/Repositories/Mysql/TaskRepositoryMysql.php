<?php 
namespace Todoist\Infra\Repositories\Mysql;

use PDO;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Domain\Entities\Task\Task;

class TaskRepositoryMysql implements TaskRepository
{
    public function __construct(private PDO $pdo) {}

    public function find(string $uuid): ?Task
    {
        $statement = $this->pdo->prepare('SELECT * FROM tasks WHERE uuid = :uuid');
        $statement->bindValue('uuid', $uuid, PDO::PARAM_STR);
        $statement->execute();
        $task = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$task) return null;

        return new Task(
            $task['uuid'],
            $task['title'],
            $task['description'],
            $task['status'],
            $task['due_date'],
            $task['created_at'],
            $task['updated_at'],
            $task['subtasks'],
            $task['userId'],
            $task['parentTaskUuid'],
            $task['priority']
        );
    }

    /**
     * Persist a task
     *
     * @param Task $task
     * @return void
     */
    public function save(Task $task): void
    {
        $this->persistOrUpdate('INSERT INTO tasks VALUES (:uuid, :title, :description, :status, :due_date, :created_at, :updated_at, :subtasks, :userId, :parentTaskUuid, :priority)', $task);
    }

    /**
     * Update a task
     *
     * @param Task $task
     * @return void
     */
    public function update(Task $task): void
    {
        $this->persistOrUpdate('UPDATE tasks SET title = :title, description = :description, due_date = :due_date, priority = :priority, updated_at = :updated_at WHERE uuid = :uuid', $task);
    }

    public function delete(Task $task): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE uuid = :uuid AND parentTaskUuid = :parentTaskUuid');
        $stmt->bindValue(':uuid', $task->uuid);
        $stmt->bindValue(':parentTaskUuid', $task->parentTaskUuid);
        $stmt->execute();
    }

    public function findAllByUserUuid(string $uuid): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM tasks WHERE userId = :uuid');
        $statement->bindValue('uuid', $uuid, PDO::PARAM_STR);
        $statement->execute();
        $tasks = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $tasks ?? null;
    }

    public function findTasksThatAreDueSoonByUserUuid(string $uuid): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE userId = :uuid AND due_date < :today');
        $stmt->bindValue('uuid', $uuid, PDO::PARAM_STR);
        $stmt->bindValue('today', date('Y-m-d'));
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $tasks ?? null;
    }

    private function persistOrUpdate(string $query, Task $task): void 
    {
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':uuid', $task->uuid);
        $stmt->bindValue(':title', $task->title);
        $stmt->bindValue(':description', $task->description);
        $stmt->bindValue(':status', $task->status);
        $stmt->bindValue(':due_date', $task->due_date);
        $stmt->bindValue(':created_at', $task->created_at);
        $stmt->bindValue(':updated_at', $task->updated_at);
        $stmt->bindValue(':subtasks', json_encode($task->subtasks));
        $stmt->bindValue(':userId', $task->userId);
        $stmt->bindValue(':parentTaskUuid', $task->parentTaskUuid);
        $stmt->bindValue(':priority', $task->priority);

        $stmt->execute();
    }
}