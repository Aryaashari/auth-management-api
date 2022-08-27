<?php

namespace Auth\Api\Repository;

use Auth\Api\Config\Database;
use Auth\Api\Model\Session;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\HostnameProcessor;

class SessionRepository {


    private \PDO $db;
    private Logger $logger;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->logger = new Logger(SessionRepository::class);
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__."/../../logs/app_log/app.log", 7));
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__."/../../logs/error_log/error.log", 7, Logger::ERROR));
        $this->logger->pushProcessor(new HostnameProcessor());
        $this->logger->pushProcessor(function ($record) {
            $record["extra"]["ipAddress"] = $_SERVER["REMOTE_ADDR"];
            return $record;
        });
    }


    public function getSession(string $id) : ?Session {
        try {

            $this->logger->info("Try to get session", ["session_id" => $id]);
            $stmt = $this->db->prepare("SELECT id FROM sessions WHERE id=?");
            $stmt->execute([$id]);
    
            if ($session = $stmt->fetch()) {
                $this->logger->info("Session found", ["session_id" => $id]);
                return new Session($session["id"]);
            }
            
            $this->logger->info("Session not found", ["session_id" => $id]);
            return null;
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }


    public function createSession(string $username) : Session {
        try {
            
            $this->logger->info("Try to create session", ["username" => $username]);
            $sesId = uniqid() . '_'. $username;
            $stmt = $this->db->prepare("INSERT INTO sessions(id) VALUES(?)");
            $stmt->execute([$sesId]);
            
            $this->logger->info("Success to create session", ["username" => $username]);
            return new Session($sesId);
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }

    public function destroySession(string $id) : bool {

        try {

            $this->logger->info("Try to destroy session", ["session_id" => $id]);
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id=?");
            $stmt->execute([$id]);

            $this->logger->info("Success to destroy session", ["session_id" => $id]);
            return true;
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }

    public function deleteAllData() : void {
        try {
            $this->logger->info("Try to delete all data session");
            $this->db->query("DELETE FROM sessions");

            $this->logger->info("Success to delete all data session");
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }


}