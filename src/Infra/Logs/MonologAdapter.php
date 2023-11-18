<?php 
namespace Todoist\Infra\Logs;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologAdapter
{
    public static function log(): Logger
    {
        $logger = new Logger('todoist');
        // $logger->pushHandler(new StreamHandler('php://stdout'));
        $logger->pushHandler(new StreamHandler(__DIR__ . "/../../../logs/app.log", Level::Warning));
        return $logger;
    }
}