<?php

namespace Auth\Api\Controller;

use Auth\Api\App\View;

class HomeController {

    public function index() {
        View::render("home.php");
    }

}