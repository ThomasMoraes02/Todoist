<?php 
namespace Todoist\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Application\UseCases\Tasks\DeleteTask\DeleteTask;
use Todoist\Application\UseCases\Tasks\DeleteTask\InputTask as DeleteTaskInputTask;
use Todoist\Infra\Repositories\Memory\TaskRepositoryMemory;

class DeleteTaskTest extends TestCase
{
    public TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = new TaskRepositoryMemory();   
    }

    public function test_must_delete_task()
    {
        $inputCreateTask = new InputTask(
            'Make Lunch',
            'Make lunch with my family',
            null
        );

        $createTask = new CreateTask($this->taskRepository);

        $outputCreateTask = $createTask->execute($inputCreateTask);

        $inputDeleteTask = new DeleteTaskInputTask(
            $outputCreateTask->uuid
        );

        $deleteTask = new DeleteTask($this->taskRepository);

        $outputDeleteTask = $deleteTask->execute($inputDeleteTask);

        $this->assertEquals(
            $outputDeleteTask->message,
            "Task {$outputCreateTask->uuid} deleted successfully"
        );

        $task = $this->taskRepository->find($outputCreateTask->uuid);
        $this->assertNull($task);
    }
}