<?php 
namespace Todoist\Application\UseCases\Tasks\CreateTask;

class InputTask
{
    /** @var InputTask[] */
    public array $subtasks = [];

    public function __construct(
        public string $title,
        public ?string $description,
        public ?string $due_date = null,
        public ?string $userId = null,
        public ?string $parentTaskUuid = null
    ) {}

    public function subtask(
        string $title, 
        ?string $description = null, 
        ?string $due_date = null, 
        ?string $userId = null, 
        ?string $parentTaskUuid = null
    ): void {
        $this->subtasks[] = new InputTask(
            $title, 
            $description, 
            $due_date, 
            $userId, 
            $parentTaskUuid
        );
    }
}