<?php 
namespace Todoist\Domain\Entities\Task;

use Exception;
use DateTimeZone;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use DateTimeInterface;

class Task
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $title,
        private readonly string $description,
        private readonly ?DateTimeInterface $due_date,
        private readonly DateTimeInterface $created_at,
        private readonly ?DateTimeInterface $updated_at,
    ) {}

    public static function create(
        ?string $uuid = null,
        string $title,
        string $description,
        ?string $due_date = null,
        ?string $created_at = null,
        ?string $updated_at = null
    ): Task {
        return new Task(
            $uuid ?? Uuid::uuid4(),
            $title,
            $description,
            $due_date ? self::date($due_date) : null,
            self::date($created_at ?? 'now'), 
            self::date($updated_at ?? 'now')
        );
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