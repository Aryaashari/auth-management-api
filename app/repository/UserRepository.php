<?php

namespace Auth\Api\Repository;

use Auth\Api\Config\Database;
use Auth\Api\Model\User;
use Monolog\Logger;
use Monolog\Processor\HostnameProcessor;
use Monolog\Handler\RotatingFileHandler;

class UserRepository {


    private \PDO $db;
    private Logger $logger;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->logger = new Logger(UserRepository::class);
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__."/../../logs/error_log/error.log", 7, Logger::ERROR));
        $this->logger->pushHandler(new RotatingFileHandler(__DIR__."/../../logs/app_log/app.log", 7));
        $this->logger->pushProcessor(new HostnameProcessor());
        $this->logger->pushProcessor(function ($record) {
            $record["extra"]["ipAddress"] = $_SERVER["REMOTE_ADDR"];
            return $record;
        });
    }

    public function findByUsername(string $username) : ?User {

        try {
            
            $this->logger->info("Try to find user by username", ["username" => $username]);
            $stmt = $this->db->prepare("SELECT id,name,username,password FROM users WHERE username=?");
            $stmt->execute([$username]);
    
            if ($user = $stmt->fetch()) {
                $this->logger->info("Username found", ["username" => $username]);
                return new User($user["id"], $user["name"], $user["username"], $user["password"]);
            }
            
            $this->logger->info("Username not found", ["username" => $username]);
            return null;
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage(), ["username" => $username]);
            throw $e;
        }


    }

    public function register(User $user) : User {

        try {

            $this->logger->info("Try to register", ["username" => $user->username]);
            $stmt = $this->db->prepare("INSERT INTO users(name,username,password) VALUES(?,?,?)");
            $stmt->execute([$user->name, $user->username, $user->password]);
            
            $user->id = $this->db->lastInsertId();
            
            $this->logger->info("Success to register", ["username" => $user->username]);
            return $user;

        } catch(\Exception $e) {
            $this->logger->error($e->getMessage(), ["username" => $user->username]);
            throw $e;
        }

    }


    public function deleteAllData() : void {
        try {

            $this->logger->info("Try to delete all data user");
            $stmt = $this->db->query("DELETE FROM users");
            $this->logger->info("Success to delete all data user");
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }


}