<?php 
namespace Todoist\Domain\ValueObjects;

use InvalidArgumentException;

class Email
{
    public function __construct(private string $email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("E-mail is invalid");
        }
    }

    public function __toString(): string
    {
        return $this->email;
    }
}