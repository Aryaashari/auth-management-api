<?php

namespace Auth\Api\Model;

class Session {

    public ?string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id;
    }

}