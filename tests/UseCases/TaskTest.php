<?php 
namespace Todoist\Test\UseCases;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Infra\Repositories\Memory\TaskRepositoryMemory;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;

class TaskTest extends TestCase
{
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = new TaskRepositoryMemory();
    }

    public function test_create_task(): void
    {
        $inputTask = new InputTask(
            'Clear the room',
            'Go to the kitchen and clean the room',
            '2023-11-11'
        );

        $outputTask = (new CreateTask($this->taskRepository))->execute($inputTask);

        $this->assertIsString($outputTask->uuid);
        $this->assertEquals('Clear the room', $outputTask->title);
        $this->assertInstanceOf(DateTimeInterface::class, $outputTask->due_date);
        $this->assertEquals('PENDING', $outputTask->status->name);
        $this->assertEquals('2023-11-11 00:00:00', $outputTask->due_date->format('Y-m-d H:i:s'));
    }
}