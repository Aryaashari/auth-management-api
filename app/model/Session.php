<?php

namespace Auth\Api\Model;

class Session {

    public ?int $id;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

}