<?php 
namespace Todoist\Test\Integration\Log;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Todoist\Infra\Logs\MonologAdapter;

class LogginTest extends TestCase
{
    public function test_create_log(): void
    {
        $logger = new Logger('todoist');
        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $monolog = MonologAdapter::log();
        $monolog->info('test');

        $this->assertTrue($handler->hasInfoRecords());
        $records = $handler->getRecords();

        $this->assertEquals('test', $records[0]['message']);
    }
}