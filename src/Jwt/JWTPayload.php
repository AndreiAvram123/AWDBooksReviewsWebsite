<?php

namespace App\Jwt;

class JWTPayload
{
    private string $email;
    private array $roles;

    /**
     * @param string $email
     * @param array $roles
     */
    public function __construct(string $email, array $roles)
    {
        $this->email = $email;
        $this->roles = $roles;
    }


    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }


    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isUserModerator():bool{
        return in_array('ROLE_MODERATOR',$this->roles);
    }


}