<?php 
namespace Todoist\Application\UseCases\Tasks\CreateTask;

use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Application\UseCases\Tasks\CreateTask\OutputTask;
use Todoist\Domain\Entities\Task\Task;

class CreateTask
{
    public function __construct(private TaskRepository $taskRepository) {}

    public function execute(InputTask $input): OutputTask
    {
        $task = Task::create(
            $input->title,
            $input->description,
            $input->due_date
        );

        if(!empty($input->subtasks)) {
            foreach($input->subtasks as $subtask) {
                $task->addSubtask(
                    Task::create(
                        $subtask->title,
                        $subtask->description,
                        $subtask->due_date,
                        $task->userId,
                        $task->uuid
                    )
                );
            }
        }

        $this->taskRepository->save($task);

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