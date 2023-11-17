<?php 
namespace Todoist\Infra\Repositories\Mysql;

use PDO;
use DateTime;
use Todoist\Domain\Entities\Task\Task;
use Todoist\Domain\Entities\Task\TaskStatusCodes;
use Todoist\Domain\Entities\Task\TaskPriorityCodes;
use Todoist\Application\Repositories\TaskRepository;

class TaskRepositoryMysql implements TaskRepository
{
    public function __construct(private PDO $pdo) {}

    public function find(string $uuid): ?Task
    {
        $statement = $this->pdo->prepare('SELECT * FROM tasks WHERE uuid = :uuid AND parentTaskUuid IS NULL');
        $statement->bindValue('uuid', $uuid, PDO::PARAM_STR);
        $statement->execute();
        $task = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$task) return null;

        $task = new Task(
            $task['uuid'],
            $task['title'],
            $task['description'],
            TaskStatusCodes::from($task['status']),
            (new DateTime($task['due_date'])),
            (new DateTime($task['created_at'])),
            (new DateTime($task['updated_at'])),
            [],
            $task['userId'],
            $task['parentTaskUuid'],
            TaskPriorityCodes::from($task['priority'])
        );

        $statement = $this->pdo->prepare('SELECT * FROM tasks WHERE parentTaskUuid = :uuid');
        $statement->bindValue('uuid', $uuid, PDO::PARAM_STR);
        $statement->execute();
        $subtasks = $statement->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($subtasks)) {
            foreach($subtasks as $subtask) {
                $task->addSubtask(
                    new Task(
                        $subtask['uuid'],
                        $subtask['title'],
                        $subtask['description'],
                        TaskStatusCodes::from($subtask['status']),
                        (new DateTime($subtask['due_date'])),
                        (new DateTime($subtask['created_at'])),
                        (new DateTime($subtask['updated_at'])),
                        [],
                        $subtask['userId'],
                        $subtask['parentTaskUuid'],
                        TaskPriorityCodes::from($subtask['priority'])
                    )
                );
            }
        }

        return $task;
    }

    /**
     * Persist a task
     *
     * @param Task $task
     * @return void
     */
    public function save(Task $task): void
    {
        $this->persistOrUpdate('INSERT INTO tasks VALUES (:uuid, :title, :description, :status, :due_date, :created_at, :updated_at, :userId, :parentTaskUuid, :priority)', $task);
    }

    /**
     * Update a task
     *
     * @param Task $task
     * @return void
     */
    public function update(Task $task): void
    {
        $this->persistOrUpdate('UPDATE tasks SET uuid = :uuid, title = :title, description = :description, status = :status, due_date = :due_date, created_at = :created_at, updated_at = :updated_at, userId = :userId, parentTaskUuid = :parentTaskUuid, priority = :priority WHERE uuid = :uuid', $task);
    }

    public function delete(Task $task): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE uuid = :uuid');
        $stmt->bindValue(':uuid', $task->uuid);
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
        $stmt->bindValue(':status', $task->status->value);
        $stmt->bindValue(':due_date', ($task->due_date) ? $task->due_date->format('Y-m-d H:i:s') : '');
        $stmt->bindValue(':created_at', ($task->created_at) ? $task->created_at->format('Y-m-d H:i:s') : '');
        $stmt->bindValue(':updated_at', ($task->updated_at) ? $task->updated_at->format('Y-m-d H:i:s') : '');
        $stmt->bindValue(':userId', $task->userId);
        $stmt->bindValue(':parentTaskUuid', $task->parentTaskUuid);
        $stmt->bindValue(':priority', $task->priority->value);

        $stmt->execute();
    }
}