<?php 
namespace Todoist\Test\Integration\UseCases\Tasks;

use PHPUnit\Framework\TestCase;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Domain\Entities\Task\TaskStatusCodes;
use Todoist\Infra\Repositories\Memory\TaskRepositoryMemory;

class SubtaskTest extends TestCase
{
    public TaskRepository $taskRepository;

    public InputTask $inputTask;

    public CreateTask $useCase;

    protected function setUp(): void
    {
        $this->taskRepository = new TaskRepositoryMemory();

        $this->inputTask = new InputTask(
            'Mercado',
            'Compras do café da manhã',
            '2023-11-11'
        );

        $this->useCase = new CreateTask($this->taskRepository);
    }

    public function test_create_subtasks(): void
    {
        $this->inputTask->subtask('Pó de Café', 'Café Pelé');
        $this->inputTask->subtask('Pão de Forma', 'Integral');

        $outputTask = $this->useCase->execute($this->inputTask);
        $task = $this->taskRepository->find($outputTask->uuid);

        $task->subtasks[0]->status(TaskStatusCodes::COMPLETED);

        $this->assertCount(2, $task->subtasks);
        $this->assertEquals('Pó de Café', $task->subtasks[0]->title);
        $this->assertEquals('COMPLETED', $task->subtasks[0]->status->name);
        $this->assertEquals($task->uuid, $task->subtasks[0]->parentTaskUuid);
    }

    public function teat_check_subtasks_statuses()
    {
        $this->inputTask->subtask('Pó de Café', 'Café Pelé');
        $this->inputTask->subtask('Pão de Forma', 'Integral');
        $this->inputTask->subtask('Iogurt', 'Integral');
        $this->inputTask->subtask('Bananas');
        $this->inputTask->subtask('Aveia');
        $this->inputTask->subtask('Granola');

        $outputTask = $this->useCase->execute($this->inputTask);
        $task = $this->taskRepository->find($outputTask->uuid);

        $task->subtasks[1]->status(TaskStatusCodes::OVERDUE);
        $task->subtasks[2]->status(TaskStatusCodes::COMPLETED);
        $task->subtasks[3]->status(TaskStatusCodes::COMPLETED);

        $this->assertEquals(6, $task->subtasksCount());
        $this->assertEquals(1, $task->subtasksOverdue());
        $this->assertEquals(2, $task->subtasksCompleted());
        $this->assertEquals(1, $task->subtasksPending());
    }
}