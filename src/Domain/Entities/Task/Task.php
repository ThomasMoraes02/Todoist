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
        private readonly string $title,
        private readonly string $description,
        private readonly TaskStatusCodes $status,
        private readonly ?DateTimeInterface $due_date,
        private readonly DateTimeInterface $created_at,
        private readonly DateTimeInterface $updated_at,
    ) {}

    public static function create(
        string $title,
        string $description,
        ?string $due_date = null,
    ): Task {
        return new Task(
            Uuid::uuid4(),
            $title,
            $description,
            TaskStatusCodes::PENDING,
            ($due_date != null) ? self::date($due_date) : null,
            self::date(), 
            self::date()
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

    public function __get($name): mixed
    {
        if(!property_exists($this, $name)) {
            throw new Exception('Property not found');
        }

        return $this->{$name};
    }
}