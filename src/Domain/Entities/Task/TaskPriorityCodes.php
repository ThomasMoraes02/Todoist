<?php 
namespace Todoist\Domain\Entities\Task;

enum TaskPriorityCodes: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case CRITICAL = 4;
}