<?php

namespace App\BookApi;


use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Type;

#[ExclusionPolicy(ExclusionPolicy::NONE)]
class VolumeInfo
{
    private string $title = "";

    private string $description = "";
    private int $pageCount = 0;

    #[Type("array<string>")]
    private ?array $categories = null;

    #[Type("array<string>")]
    private ?array $authors = null;

    #[Type("App\BookApi\GoogleBookImages")]
    private ?GoogleBookImages $imageLinks = null;

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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    /**
     * @param int $pageCount
     */
    public function setPageCount(int $pageCount): void
    {
        $this->pageCount = $pageCount;
    }

    /**
     * @return array
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array$categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return GoogleBookImages
     */
    public function getImageLinks(): ?GoogleBookImages
    {
        return $this->imageLinks;
    }

    /**
     * @return array|null
     */
    public function getAuthors(): ?array
    {
        return $this->authors;
    }



}