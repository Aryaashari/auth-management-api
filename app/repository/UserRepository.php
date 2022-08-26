<?php

namespace Auth\Api\Repository;

use Auth\Api\Config\Database;
use Auth\Api\Model\User;

class UserRepository {


    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername(string $username) : ?User {

        try {

            $stmt = $this->db->prepare("SELECT id,name,username,password FROM users WHERE username=?");
            $stmt->execute([$username]);
    
            if ($user = $stmt->fetch()) {
                return new User($user["id"], $user["name"], $user["username"], $user["password"]);
            }
    
            return null;
        } catch(\Exception $e) {
            throw $e;
        }


    }

    public function register(User $user) : User {

        try {

            $stmt = $this->db->prepare("INSERT INTO users(name,username,password) VALUES(?,?,?)");
            $stmt->execute([$user->name, $user->username, $user->password]);
            
            $user->id = $this->db->lastInsertId();
    
            return $user;

        } catch(\Exception $e) {
            throw $e;
        }

    }


    public function deleteAllData() : void {
        try {
            $stmt = $this->db->query("DELETE FROM users");
        } catch(\Exception $e) {
            throw $e;
        }
    }


}