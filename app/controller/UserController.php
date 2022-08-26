<?php

namespace Auth\Api\Controller;

use Auth\Api\Exception\ValidationException;
use Auth\Api\Model\User;
use Auth\Api\Repository\SessionRepository;
use Auth\Api\Repository\UserRepository;

class UserController {


    private UserRepository $userRepo;
    private SessionRepository $sesRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository;
        $this->sesRepo = new SessionRepository;
    }


    public function register() {

        $name = htmlspecialchars(trim($_POST["name"] ?? ""));
        $username = htmlspecialchars(trim($_POST["username"] ?? ""));
        $password = htmlspecialchars(trim($_POST["password"] ?? ""));
        $confirmPassword = htmlspecialchars(trim($_POST["password_confirmation"] ?? ""));

        try {
             // Validasi name
            if ($name == "") {
                throw new ValidationException("Name is required");
            }

            // Validasi username
            if ($username == "") {
                throw new ValidationException("Username is required");
            }

            $usernameCheck = $this->userRepo->findByUsername($username);

            if ($usernameCheck != null) {
                throw new ValidationException("Username has alredy exist");
            }

            // Validasi Password
            if ($password == "") {
                throw new ValidationException("Password is required");
            } else if (strlen($password) < 8) {
                throw new ValidationException("Password min 8 character");
            }

            // Validasi confirm password
            if ($password !== $confirmPassword) {
                throw new ValidationException("Confirmation password and password is diferent");
            }


            $user = $this->userRepo->register(new User(null, $name, $username, $password));

            http_response_code(200);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "Create user successfuly",
                "error" => null,
                "data" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "username" => $user->username
                ]
            ]);
        } catch(ValidationException $e) {
            http_response_code(400);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "error",
                "code" => 400,
                "message" => "Failed to create user",
                "error" => $e->getMessage(),
                "data" => null
            ]);
        } catch(\Exception $e) {
            http_response_code(500);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "error",
                "code" => 500,
                "message" => "Something went error",
                "error" => $e,
                "data" => null
            ]);
        }
    }


}