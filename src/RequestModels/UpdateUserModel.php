<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class UpdateUserModel
{
    #[NotBlank]
    public string $username = "";
    #[NotBlank]
    public ?string $nickname = null;

    #[NotBlank]
    #[Email(
        message: 'The email {{ value }} is not a valid email.',
    )]

    public string $email = "";

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


}