<?php

namespace Auth\Api\Model;


class User {

    public ?int $id;
    public ?string $name, $username, $password, $role;

    public function __construct(?int $id = null, ?string $name = null, ?string $username = null, ?string $password = null, ?string $role = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

}