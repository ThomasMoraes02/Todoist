<?php 
namespace Todoist\Application\UseCases\Tasks\UpdateTask;

class InputTask
{
    /** @var InputTask[] */
    public array $subtasks = [];

    public function __construct(
        public string $uuid,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $due_date = null,
        public ?string $priority = null
    ) {}
}