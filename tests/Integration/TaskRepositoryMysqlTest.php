<?php 
namespace Todoist\Test\Integration;

use PDO;
use DateTime;
use DateTimeZone;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Todoist\Domain\Entities\Task\Task;
use Todoist\Domain\Entities\Task\TaskPriorityCodes;
use Todoist\Application\Repositories\TaskRepository;
use Todoist\Infra\Repositories\Mysql\TaskRepositoryMysql;
use Todoist\Application\UseCases\Tasks\CreateTask\InputTask;
use Todoist\Application\UseCases\Tasks\CreateTask\CreateTask;
use Todoist\Application\UseCases\Tasks\DeleteTask\DeleteTask;
use Todoist\Application\UseCases\Tasks\UpdateTask\UpdateTask;
use Todoist\Application\UseCases\Tasks\DeleteTask\InputTask as DeleteTaskInputTask;
use Todoist\Application\UseCases\Tasks\UpdateTask\InputTask as UpdateTaskInputTask;

class TaskRepositoryMysqlTest extends TestCase
{
    public static PDO $pdo;

    public static TaskRepository $taskRepository;

    public static DateTimeInterface $now;

    public Task $task;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec('CREATE TABLE IF NOT EXISTS tasks (uuid TEXT, title TEXT, description TEXT, status TEXT, due_date TEXT, created_at TEXT, updated_at TEXT, userId TEXT, parentTaskUuid TEXT, priority TEXT)');

        self::$taskRepository = new TaskRepositoryMysql(self::$pdo);
        self::$now = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')));
    }

    /**
     * Create a new task and SetUp it
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::$pdo->beginTransaction();

        $inputTask = new InputTask(
            'Mercado',
            'Lista de compras do Mercado',
            self::$now->format('Y-m-d H:i:s'),
            null,
            null
        );

        $useCase = new CreateTask(self::$taskRepository);

        $output = $useCase->execute($inputTask);

        $task = self::$taskRepository->find($output->uuid);

        $this->task = $task;

        $this->assertEquals('Mercado', $task->title);
        $this->assertEquals('Lista de compras do Mercado', $task->description);
        $this->assertEquals(self::$now->format('Y-m-d H:i:s'), $task->due_date->format('Y-m-d H:i:s'));
    }

    public function test_update_task()
    {
        $inputTask = new UpdateTaskInputTask(
            $this->task->uuid,
            null,
            'Lista de compras do mês',
            null,
            TaskPriorityCodes::HIGH->value
        );

        $output = (new UpdateTask(self::$taskRepository))->execute($inputTask);

        $task = self::$taskRepository->find($output->uuid);

        $this->assertEquals('Mercado', $task->title);
        $this->assertEquals('Lista de compras do mês', $task->description);
    }

    public function test_delete_task()
    {
        $inputTask = new DeleteTaskInputTask(
            $this->task->uuid
        );

        (new DeleteTask(self::$taskRepository))->execute($inputTask);

        $task = self::$taskRepository->find($this->task->uuid);

        $this->assertNull($task);
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}