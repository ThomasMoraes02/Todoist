<?php 
namespace Todoist\Application\UseCases\Tasks\UpdateTask;

use DomainException;
use Todoist\Domain\Entities\Task\Task;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\UpdateTask\InputTask;
use Todoist\Application\UseCases\Tasks\UpdateTask\OutputTask;

class UpdateTask
{
    public function __construct(private TaskRepository $taskRepository) {}

    public function execute(InputTask $input): OutputTask
    {
        $task = $this->taskRepository->find($input->uuid);
        if (!$task) throw new DomainException('Task not found');

        $task = $task->update(
            $input->title ?? $task->title,
            $input->description ?? $task->description,
            $input->due_date ?? $task->due_date->format('Y-m-d H:i:s'),
            $input->priority ?? $task->priority->value
        );

        $this->taskRepository->update($task);

        return new OutputTask(
            $task->uuid,
            $task->title,
            $task->description,
            $task->userId,
            $task->parentTaskUuid,
            $task->subtasks,
            $task->due_date,
            $task->status,
            $task->created_at,
            $task->updated_at
        );
    }
}