<?php

namespace App\RequestModels;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Stopwatch\Section;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class CreateBookReviewModel
{

    private  int $bookID = 0;
    private ?string $googleBookID = null;

    #[NotBlank]
    private string $title;

    #[NotNull]
    #[Type("array<App\Entity\ReviewSection>")]
    private $sections;
    #[NotBlank]
    private string $base64Image;

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
     * @return [
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
    public function getBase64Image(): string
    {
        return $this->base64Image;
    }

    /**
     * @param string $base64Image
     */
    public function setBase64Image(string $base64Image): void
    {
        $this->base64Image = $base64Image;
    }

    /**
     * @return string
     */
    public function getGoogleBookID(): ?string
    {
        return $this->googleBookID;
    }



}