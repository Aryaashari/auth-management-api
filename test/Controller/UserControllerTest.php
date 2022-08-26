<?php

namespace Auth\Api\Controller;

require_once __DIR__."/../../vendor/autoload.php";

use Auth\Api\Controller\UserController;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase {


    private UserController $userController;

    public function setUp() : void {
        $this->userController = new UserController;
    }


    public function testRegisterSuccess() : void {
        $this->userController->register();
    }


}