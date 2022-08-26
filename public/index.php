<?php

require_once __DIR__."/../vendor/autoload.php";

use Auth\Api\App\Route;
use Auth\Api\Config\Database;
use Auth\Api\Controller\HomeController;
use Auth\Api\Controller\UserController;

Database::getConnection("mysql", "production");
Route::get("/", HomeController::class, "index", []);

// User
Route::post("/api/users/register", UserController::class, "register", []);
Route::post("/api/users/login", UserController::class, "login", []);
Route::post("/api/users/logout", UserController::class, "logout", []);
Route::get("/api/users", UserController::class, "detail", []);



Route::run();