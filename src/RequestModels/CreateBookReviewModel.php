<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Stopwatch\Section;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class CreateBookReviewModel
{
    #[NotNull]
    private  int $bookID;
    #[NotNull]
    private bool $pending;
    #[NotNull]
    private bool $declined;
    #[NotBlank]
    private string $title;
    #[NotNull]
    private $sections;
    #[NotBlank]
    private string $base64image;
    #[NotNull]
    private int $userID;

    /**
     * @return int
     */
    public function getBookID(): int
    {
        return $this->bookID;
    }

    /**
     * @param int $bookID
     */
    public function setBookID(int $bookID): void
    {
        $this->bookID = $bookID;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->pending;
    }

    /**
     * @param bool $pending
     */
    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->declined;
    }

    /**
     * @param bool $declined
     */
    public function setDeclined(bool $declined): void
    {
        $this->declined = $declined;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param mixed $sections
     */
    public function setSections($sections): void
    {
        $this->sections = $sections;
    }

    /**
     * @return string
     */
    public function getBase64image(): string
    {
        return $this->base64image;
    }

    /**
     * @param string $base64image
     */
    public function setBase64image(string $base64image): void
    {
        $this->base64image = $base64image;
    }

    /**
     * @return int
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @param int $userID
     */
    public function setUserID(int $userID): void
    {
        $this->userID = $userID;
    }


}