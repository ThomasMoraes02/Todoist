<?php 
namespace Todoist\Domain\Entities\Task;

enum TaskStatusCodes : int
{
    case PENDING = 1;
    case OVERDUE = 2;
    case COMPLETED = 3;
}