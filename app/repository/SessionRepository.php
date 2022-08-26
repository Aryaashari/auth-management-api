<?php

namespace Auth\Api\Repository;

use Auth\Api\Config\Database;
use Auth\Api\Model\Session;

class SessionRepository {


    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }


    public function getSession(string $id) : ?Session {
        try {

            $stmt = $this->db->prepare("SELECT id FROM sessions WHERE id=?");
            $stmt->execute([$id]);
    
            if ($session = $stmt->fetch()) {
                return new Session($session["id"]);
            }
    
            return null;
        } catch(\Exception $e) {
            throw $e;
        }
    }


    public function createSession(string $username) : Session {
        try {
            
            $sesId = uniqid() . '_'. $username;
            $stmt = $this->db->prepare("INSERT INTO sessions(id) VALUES(?)");
            $stmt->execute([$sesId]);
    
            return new Session($sesId);
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function destroySession(string $id) : bool {

        try {

            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id=?");
            $stmt->execute([$id]);
            return true;
        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function deleteAllData() : void {
        try {
            $this->db->query("DELETE FROM sessions");
        } catch(\Exception $e) {
            throw $e;
        }
    }


}