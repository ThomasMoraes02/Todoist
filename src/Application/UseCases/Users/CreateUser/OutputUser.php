<?php 
namespace Todoist\Application\UseCases\Users\CreateUser;

class OutputUser
{
    public function __construct(
        public string $uuid
    ) {}
}