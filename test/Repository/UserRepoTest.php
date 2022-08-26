<?php

namespace Auth\Api\Repository;

require_once __DIR__."/../../vendor/autoload.php";

use Auth\Api\Config\Database;
use Auth\Api\Model\User;
use PHPUnit\Framework\TestCase;

class UserRepoTest extends TestCase{


    private UserRepository $userRepo;
    private \PDO $db;

    public function setUp() : void {
        $this->userRepo = new UserRepository;
        $this->db = Database::getConnection();
        $this->userRepo->deleteAllData();
    }

    public function testFindUsernameFound() : void {

        $pass = password_hash('12345678', PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users(name,username,password) VALUES ('Arya', 'aryaashari', ?)");
        $stmt->execute([$pass]);

        $user = $this->userRepo->findByUsername("aryaashari");
        var_dump($user);
        $this->assertIsObject($user);
    }

    public function testFindUsernameNotFound() : void {

        $user = $this->userRepo->findByUsername("aryaashari");
        var_dump($user);
        $this->assertNull($user);
    }

    public function testRegister() : void {
        $user = new User(null, "Arya Ashari", "arya", password_hash("password", PASSWORD_BCRYPT));
        $user = $this->userRepo->register($user);
        var_dump($user);
        $this->assertIsObject($user);
    }


}