<?php 
namespace Todoist\Domain\Entities;

interface Encoder
{
    public function encode(string $password): string;

    public function decode(string $password, string $hash): bool;
}