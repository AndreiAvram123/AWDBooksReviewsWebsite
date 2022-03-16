<?php

namespace App\Entity;

use App\Repository\GoogleBookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;

#[ORM\Entity(repositoryClass: GoogleBookRepository::class)]
class GoogleBook extends Book
{

    #[ORM\Column(type: 'string', nullable: false)]
    private string $googleBookID;

    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'googleBooks')]
    private ArrayCollection $categories;

    #[ORM\ManyToOne(targetEntity: BookAuthor::class, inversedBy: 'books')]
    private $author;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookReview::class, orphanRemoval: true)]
    #[Exclude]
    private  $bookReviews;

    #[Pure] public function __construct()
    {
        $this->categories = new ArrayCollection();
    }


    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(BookCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(BookCategory $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleBookID(): string
    {
        return $this->googleBookID;
    }

    /**
     * @param string $googleBookID
     */
    public function setGoogleBookID(string $googleBookID): void
    {
        $this->googleBookID = $googleBookID;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getBookReviews()
    {
        return $this->bookReviews;
    }

    /**
     * @param mixed $bookReviews
     */
    public function setBookReviews($bookReviews): void
    {
        $this->bookReviews = $bookReviews;
    }



}
