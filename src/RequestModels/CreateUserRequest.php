<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Property;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use OpenApi\Annotations as OA;


#[ExclusionPolicy(ExclusionPolicy::NONE)]
class CreateUserRequest
{
    #[NotBlank]
    /**
     * @OA\Property(description="The username of the user", example="andrei1239")
     */
    private string $username;

    #[NotBlank]
    #[Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    /**
     * @OA\Property(description="The email of the user", example="avramandreitiberiu@gmail.com")
     */
    private string $email = "";
    #[NotBlank]
    /**
     * @OA\Property(description="The password of the user", example="andrei1239")
     */
    private string $password;

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

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }


}