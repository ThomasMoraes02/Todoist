<?php 
namespace Todoist\Domain\Factories;

use Todoist\Domain\Entities\Encoder;
use Todoist\Domain\Entities\User;

class UserFactory
{
    public function __construct(private Encoder $encoder) {}

    /**
     * Create a new user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $name, string $email, string $password): User
    {
        return User::create($name, $email, $password, $this->encoder);
    }

    /**
     * Restore a user
     *
     * @param string $uuid
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     */
    public function restore(string $uuid, string $name, string $email, string $password): User
    {
        return User::restore($uuid, $name, $email, $password, $this->encoder);
    }
}