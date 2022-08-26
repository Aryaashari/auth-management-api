<?php

namespace Auth\Api\Controller;

use Auth\Api\Config\App;
use Auth\Api\Exception\ValidationException;
use Auth\Api\Model\User;
use Auth\Api\Repository\SessionRepository;
use Auth\Api\Repository\UserRepository;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use UnexpectedValueException;

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


            $user = $this->userRepo->register(new User(null, $name, $username, password_hash($password, PASSWORD_BCRYPT)));

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


    public function login() : void {

        $username = htmlspecialchars(trim($_POST["username"] ?? ""));
        $password = htmlspecialchars(trim($_POST["password"] ?? ""));
        

        try {
            // Validasi username
            if ($username == "") {
                throw new ValidationException("Username is required");
            }

            $user = $this->userRepo->findByUsername($username);

            if ($user == null) {
                throw new ValidationException("Username or password invalid");
            }

            // Validasi password
            if ($password == "") {
                throw new ValidationException("Password is required");
            }

            if (!password_verify($password, $user->password)) {
                throw new ValidationException("Username or password invalid");
            }

            $session = $this->sesRepo->createSession($user->username);

            $payload = [
                "session_id" => $session->id,
                "username" => $user->username
            ];

            $jwt = JWT::encode($payload, App::$secretKey, 'HS256');

            http_response_code(200);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "Login user successfuly",
                "error" => null,
                "data" => [
                    "token" => $jwt,
                    "user" => [
                        "id" => $user->id,
                        "name" => $user->name,
                        "username" => $user->username
                    ]
                ]
            ]);
        } catch(ValidationException $e) {
            http_response_code(400);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "error",
                "code" => 400,
                "message" => "Failed to login user",
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

    public function detail() : void {

        try {
            $token = $_SERVER["HTTP_TOKEN"] ?? "";
            if ($token == "") {
                throw new ValidationException("Token is empty");
            }

            $decode  = JWT::decode($token, new Key(App::$secretKey, 'HS256'));

            $session = $this->sesRepo->getSession($decode->session_id);
            if ($session == null) {
                throw new ValidationException("Token is invalid");
            }

            $user = $this->userRepo->findByUsername($decode->username);
            http_response_code(200);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "Get Detail User Successfuly",
                "error" => null,
                "data" => [
                    "id" => $user->id,
                    "name" => $user->name,
                    "username" => $user->username
                ]
            ]);
        } catch(ValidationException | UnexpectedValueException | SignatureInvalidException | BeforeValidException | ExpiredException $e) {
            http_response_code(400);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "error",
                "code" => 400,
                "message" => "Unauthorized",
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

    public function logout() : void {

        try {

            $token = $_SERVER["HTTP_TOKEN"] ?? "";
            if ($token == "") {
                throw new ValidationException("Token is empty");
            }
    
            $decode  = JWT::decode($token, new Key(App::$secretKey, 'HS256'));
    
            $session = $this->sesRepo->getSession($decode->session_id);
            if ($session == null) {
                throw new ValidationException("Token is invalid");
            }
    
            $this->sesRepo->destroySession($session->id);
            
            http_response_code(200);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "Logout Successfuly",
                "error" => null,
                "data" => null
            ]);

        } catch(ValidationException | UnexpectedValueException | SignatureInvalidException | BeforeValidException | ExpiredException $e) {
            http_response_code(400);
            header("Content-type: application/json");
            echo json_encode([
                "status" => "error",
                "code" => 400,
                "message" => "Unauthorized",
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