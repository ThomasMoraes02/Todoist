<?php 
namespace Todoist\Test\Integration\UseCases\Tasks;

use DateTime;
use DateTimeZone;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Todoist\Domain\Entities\Task\TaskStatusCodes;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Infra\Repositories\Memory\TaskRepositoryMemory;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;
use Todoist\Domain\Entities\Task\TaskPriorityCodes;
use Todoist\Domain\Entities\User;
use Todoist\Infra\Encoders\EncoderArgon2Id;

class CreateTaskTest extends TestCase
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
        $this->assertEquals('OVERDUE', $outputTask->status->name);
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

    public function test_when_create_and_update_priority()
    {
        $inputTask = new InputTask(
            'Clear the room',
            'Go to the kitchen and clean the room',
            '2023-11-09'
        );

        $outputTask = (new CreateTask($this->taskRepository))->execute($inputTask);
        $task = $this->taskRepository->find($outputTask->uuid);

        $task->priority(TaskPriorityCodes::CRITICAL);

        $this->assertEquals('CRITICAL', $task->priority->name);
    }

    /**
     * @dataProvider tasks
     *
     * @param array $tasks
     * @return void
     */
    public function test_must_find_tasks_that_are_due_soon(array $tasks)
    {
        $user = User::create('Thomas', 'thomas@gmail.com', '123456', new EncoderArgon2Id());

        foreach ($tasks as $task) {
            $inputTask = new InputTask(
                $task['title'],
                $task['description'],
                $task['due_date'],
                $user->uuid
            );

            $outputsTasks[] = (new CreateTask($this->taskRepository))->execute($inputTask);
        }

        $tasksDueSoon = $this->taskRepository->findTasksThatAreDueSoonByUserUuid($user->uuid);
        
        $this->assertCount(2, $tasksDueSoon);
    }

    public static function tasks(): array
    {
        $today = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d H:i:s');
        $todayMore20days = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->modify('+20 days')->format('Y-m-d H:i:s');

        return [
            [
                [
                    [
                        'title' => 'Task 1',
                        'description' => 'Description 1',
                        'due_date' => $todayMore20days
                    ],
                    [
                        'title' => 'Task 2',
                        'description' => 'Description 2',
                        'due_date' => $todayMore20days
                    ],
                    [
                        'title' => 'Task 3',
                        'description' => 'Description 3',
                        'due_date' => $today
                    ]
                ]
            ],
        ];
    }
}   