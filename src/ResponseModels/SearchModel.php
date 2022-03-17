<?php

namespace App\ResponseModels;

use App\Entity\Book;
use JMS\Serializer\Annotation\ExclusionPolicy;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class SearchModel
{
    private int $bookID = 0;
    private ?string $googleBookID = null;
    private ?string $title = null;

    public static function convertBookToSearchModel(
        Book $book
    ):SearchModel{
        $searchModel = new SearchModel();
        $searchModel->setGoogleBookID($book->getGoogleBookID());
        $searchModel->setTitle($book->getTitle());
        $searchModel->setBookID($book->getId());
        return $searchModel;
    }

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
     * @return string|null
     */
    public function getGoogleBookID(): ?string
    {
        return $this->googleBookID;
    }

    /**
     * @param string|null $googleBookID
     */
    public function setGoogleBookID(?string $googleBookID): void
    {
        $this->googleBookID = $googleBookID;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }


}