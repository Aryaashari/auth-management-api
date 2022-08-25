<?php

namespace Auth\Api;

require_once __DIR__."/../vendor/autoload.php";

use PHPUnit\Framework\TestCase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtTokenTest extends TestCase {


    public function testGenerateToken() {

        $key = "AryaAshari";
        $payload = [
            "username" => "aryaashari"
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');
        var_dump($jwt);

        $decode = JWT::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFyeWFhc2hhcmkifQ.LUT-T3d7vxOq1dGWsuUmQeNSttzoe9iy7Ha9rNY8JF4", new Key($key, 'HS256'));
        $this->assertTrue(true);

    }

}