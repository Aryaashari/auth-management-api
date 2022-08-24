<?php

require_once __DIR__."/../vendor/autoload.php";

use Auth\Api\App\Route;
use Auth\Api\Controller\HomeController;

Route::get("/", HomeController::class, "index", []);
Route::run();