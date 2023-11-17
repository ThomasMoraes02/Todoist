<?php 
namespace Todoist\Application\UseCases\Users\UpdateUser;

class OutputUser
{
    public function __construct(public string $uuid) {}
}