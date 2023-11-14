<?php 
namespace Todoist\Application\UseCases\Users\CreateUser;

class InputUser
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}
}