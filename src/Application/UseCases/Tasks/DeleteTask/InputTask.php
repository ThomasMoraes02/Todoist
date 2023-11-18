<?php 
namespace Todoist\Application\UseCases\Tasks\DeleteTask;

class InputTask
{
    public function __construct(
        public string $uuid
    ) {}
}