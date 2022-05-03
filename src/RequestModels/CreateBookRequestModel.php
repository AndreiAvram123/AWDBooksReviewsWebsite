<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class CreateBookRequestModel
{
    #[NotBlank(message: "The title should not be empty or null")]
    private string $title;
    #[Type("array<integer>")]
    #[NotBlank(message: "You must not pass an empty array")]
    private array $categoriesIDs;
    #[Type("array<integer>")]
    #[NotBlank(message: "You must not pass an empty array")]
    private array $authorsIDs;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getCategoriesIDs(): array
    {
        return $this->categoriesIDs;
    }

    /**
     * @return array
     */
    public function getAuthorsIDs(): array
    {
        return $this->authorsIDs;
    }


}