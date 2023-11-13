<?php 
namespace Todoist\Application\UseCases\Tasks\UpdateTask;

use DateTimeInterface;
use Todoist\Domain\Entities\Task\TaskStatusCodes;

class OutputTask
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?string $userId,
        public readonly ?string $parentTaskUuid,
        public readonly array $subtasks,
        public readonly ?DateTimeInterface $due_date,
        public readonly TaskStatusCodes $status,
        public readonly DateTimeInterface $created_at,
        public readonly DateTimeInterface $updated_at
    ) {}
}