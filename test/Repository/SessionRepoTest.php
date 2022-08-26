<?php

namespace Auth\Api\Repository;

require_once __DIR__."/../../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Auth\Api\Repository\SessionRepository;


class SessionRepoTest extends TestCase {

    private SessionRepository $sesRepo;

    public function setUp() : void
    {
        $this->sesRepo = new SessionRepository;
        $this->sesRepo->deleteAllData();
    }


    public function testGenrateSessionId() : void {
        $sesId = uniqid();
        var_dump($sesId);
        $this->assertTrue(true);
    }


    public function testCreateSession() : void {
        $session = $this->sesRepo->createSession("aryaashari");
        var_dump($session);
        $this->assertIsObject($session);
    }

    public function testGetSession() : void {
        $session = $this->sesRepo->createSession("aryaashari");
        var_dump($session);

        $session2 = $this->sesRepo->getSession($session->id);
        var_dump($session2);

        $this->assertEquals($session->id, $session2->id);
    }

    public function testGetSessionNotFound() : void {
        $session2 = $this->sesRepo->getSession("asdsd");
        var_dump($session2);

        $this->assertNull($session2);
    }


}