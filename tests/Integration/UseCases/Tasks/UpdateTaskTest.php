<?php 
namespace Todoist\Test\Integration\UseCases\Tasks;

use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask as CreateTaskInputTask;
use Todoist\Infra\Repositories\Memory\TaskRepositoryMemory;
use Todoist\Application\UseCases\Tasks\UpdateTask\InputTask;
use Todoist\Application\UseCases\Tasks\UpdateTask\UpdateTask;

class UpdateTaskTest extends TestCase
{
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = new TaskRepositoryMemory();
    }

    public function test_create_task(): void
    {
        $inputTask = new CreateTaskInputTask(
            'Clear the kitchen',
            'Go to the kitchen and clean the room',
            '2023-11-11'
        );

        $outputTask = (new CreateTask($this->taskRepository))->execute($inputTask);

        $currentUuid = $outputTask->uuid;
        $currentCreatedAt = $outputTask->created_at;

        $inputUpdateTask = new InputTask(
            $outputTask->uuid,
            'Clear the room',
            'Only clear the room because i am tired',
        );

        $outputTask = (new UpdateTask($this->taskRepository))->execute($inputUpdateTask);

        $this->assertSame($currentUuid, $outputTask->uuid);
        $this->assertTrue($currentCreatedAt == $outputTask->created_at);
        $this->assertEquals('Clear the room', $outputTask->title);
        $this->assertEquals('Only clear the room because i am tired', $outputTask->description);
    }
}