<?php 
namespace Todoist\Application\UseCases\Tasks\DeleteTask;

use DomainException;
use Todoist\Domain\Entities\Task\Task;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\DeleteTask\InputTask;
use Todoist\Application\UseCases\Tasks\DeleteTask\OutputTask;

class DeleteTask
{
    public function __construct(private TaskRepository $taskRepository) {}

    public function execute(InputTask $input): OutputTask
    {
        $task = $this->taskRepository->find($input->uuid);
        if (!$task) throw new DomainException('Task not found');

        $this->taskRepository->delete($task);

        return new OutputTask(
            "Task {$task->uuid} deleted successfully"  
        );
    }
}