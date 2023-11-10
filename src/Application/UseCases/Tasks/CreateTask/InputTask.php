<?php 
namespace Todoist\Application\UseCases\Tasks\CreateTask;

class InputTask
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $due_date
    ) {}
}