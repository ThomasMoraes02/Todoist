<?php 
namespace Todoist\Application\UseCases\Users\UpdateUser;

class InputUser
{
    public function __construct(
        public string $uuid,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}
}