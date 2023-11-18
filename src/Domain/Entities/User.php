<?php
namespace Todoist\Domain\Entities;

use Exception;
use Ramsey\Uuid\Uuid;
use Todoist\Domain\ValueObjects\Email;

class User
{
    public function __construct(
        private readonly string $uuid,
        private string $name,
        private Email $email,
        private string $password,
        private Encoder $encoder
    ) {}

    public static function create(string $name, string $email, string $password, Encoder $encoder): User
    {
        return new User(
            Uuid::uuid4(),
            $name,
            new Email($email),
            $encoder->encode($password),
            $encoder
        );
    }

    public function update(string $name, string $email, string $password, Encoder $encoder): User
    {
        return new User(
            $this->uuid,
            $name,
            new Email($email),
            $encoder->encode($password),
            $encoder
        );
    }

    public function __get(string $name)
    {
        if(!property_exists($this, $name)) {
            throw new Exception("Property {$name} does not exist");       
        }

        return $this->$name;
    }
}