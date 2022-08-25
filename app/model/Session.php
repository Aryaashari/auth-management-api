<?php

namespace Auth\Api\Model;

class Session {

    public ?int $id, $user_id;
    public ?string $token;

    public function __construct(?int $id = null, ?int $user_id = null, ?string $token = null)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->token = $token;
    }

}