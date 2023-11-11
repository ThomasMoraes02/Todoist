<?php 
namespace Todoist\Domain\Entities\Task;

use Exception;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use DateTimeInterface;
use Todoist\Domain\Entities\Task\TaskStatusCodes;

class Task
{
    public function __construct(
        private readonly string $uuid,
        private string $title,
        private ?string $description,
        private TaskStatusCodes $status,
        private ?DateTimeInterface $due_date,
        private readonly DateTimeInterface $created_at,
        private DateTimeInterface $updated_at,

        /** @var Task[] $subtasks */
        private array $subtasks = [],

        /** @var string|null $userId */
        private ?string $userId = null,

        private readonly ?string $parentTaskUuid = null,
    ) {}

    /**
     * Create a new Task
     *
     * @param string $title
     * @param string $description
     * @param string|null $due_date
     * @return Task
     */
    public static function create(
        string $title,
        string $description,
        ?string $due_date = null,
        ?string $userId = null,
        ?string $parentTaskUuid = null
    ): Task {

        $due_date = ($due_date != null) ? self::date($due_date) : null;
        $status = ($due_date && $due_date < self::date()) ? TaskStatusCodes::OVERDUE : TaskStatusCodes::PENDING;

        return new Task(
            Uuid::uuid4(),
            $title,
            $description ?? null,
            $status,
            $due_date,
            self::date(), 
            self::date(),
            [],
            $userId,
            $parentTaskUuid
        );
    }

    public function status(TaskStatusCodes $status): void
    {
        $this->status = $status;
        $this->updated_at = self::date(); 
    }

    private static function date(string $date = 'now'): ?DateTimeImmutable
    {
        return new DateTimeImmutable($date, new DateTimeZone('America/Sao_Paulo'));
    }

    public function addSubtask(Task $subtask): self
    {
        $this->subtasks[] = $subtask;
        return $this;
    }

    public function __get($name): mixed
    {
        if(!property_exists($this, $name)) {
            throw new Exception('Property not found');
        }

        return $this->{$name};
    }
}