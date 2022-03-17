<?php

namespace App\Entity;

use App\Repository\GoogleBooksLocalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;

#[ORM\Entity(repositoryClass: GoogleBooksLocalRepository::class)]
class GoogleBook extends Book
{

    #[ORM\Column(type: 'string', nullable: false)]
    private string $googleBookID;

    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'googleBooks')]
    private  $categories;

    #[ORM\ManyToOne(targetEntity: BookAuthor::class, inversedBy: 'books')]
    private $author;

    #[ORM\OneToMany(mappedBy: 'googleBook', targetEntity: BookReview::class)]
    private $reviews;


    #[Pure] public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    /**
     * @return Collection|BookReview[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(BookReview $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setGoogleBook($this);
        }

        return $this;
    }

    public function removeReview(BookReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getGoogleBook() === $this) {
                $review->setGoogleBook(null);
            }
        }

        return $this;
    }



}
