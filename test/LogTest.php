<?php

namespace Auth\Api;

require_once __DIR__."/../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogTest extends TestCase {


    public function testLogger() : void {

        $logger = new Logger(LogTest::class);
        var_dump($logger);
        $this->assertNotNull($logger);

    }

    public function testHandler() : void {

        $logger = new Logger(LogTest::class);
        $logger->pushHandler(new StreamHandler(__DIR__."/../app.log"));
        $logger->pushHandler(new StreamHandler("php://stderr"));

        $logger->info("Log pertama");

        $this->assertTrue(true);

    }


}