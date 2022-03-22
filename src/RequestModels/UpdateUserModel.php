<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints\Email;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class UpdateUserModel
{
    public ?string $username = null;
    public ?string $nickname = null;

    #[Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public ?string $email = null;

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     */
    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }



}