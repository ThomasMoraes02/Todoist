<?php 
namespace Todoist\Application\UseCases\Tasks\DeleteTask;

class OutputTask
{
    public function __construct(
        public string $message
    ) {}
}