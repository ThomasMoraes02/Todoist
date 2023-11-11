<?php 
namespace Todoist\Test\UseCases;

use DateTime;
use DateTimeZone;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Todoist\Domain\Entities\Task\TaskStatusCodes;
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

    public function test_update_status_task_and_update_field_updated_at()
    {
        $inputTask = new InputTask(
            'Clear the room',
            'Go to the kitchen and clean the room',
            '2023-11-11'
        );

        $outputTask = (new CreateTask($this->taskRepository))->execute($inputTask);

        $task = $this->taskRepository->find($outputTask->uuid);
        $task->status(TaskStatusCodes::COMPLETED);

        $now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');

        $this->assertEquals('COMPLETED', $task->status->name);
        $this->assertInstanceOf(DateTimeInterface::class, $task->updated_at);
        $this->assertEquals($now, $task->updated_at->format('Y-m-d H:i:s'));
    }

    public function test_when_tasks_is_latest()
    {
        $inputTask = new InputTask(
            'Clear the room',
            'Go to the kitchen and clean the room',
            '2023-11-09'
        );

        $outputTask = (new CreateTask($this->taskRepository))->execute($inputTask);

        $task = $this->taskRepository->find($outputTask->uuid);

        $this->assertEquals('2023-11-09 00:00:00', $task->due_date->format('Y-m-d H:i:s'));
        $this->assertEquals('OVERDUE', $task->status->name);
    }
}