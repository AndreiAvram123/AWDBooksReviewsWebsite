<?php

namespace App\Entity;

use App\Repository\ExclusiveBookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToOne;
use JMS\Serializer\Annotation\Exclude;

#[ORM\Entity(repositoryClass: ExclusiveBookRepository::class)]
class ExclusiveBook extends Book
{
    #[Column(type: 'integer')]
    private int $numberOfPages;

    #[OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $image;

    #[ORM\ManyToOne(targetEntity: BookAuthor::class, inversedBy: 'books')]
    private $author;

    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'books')]
    private $categories;

    #[ORM\OneToMany(mappedBy: 'exclusiveBook', targetEntity: BookReview::class)]
    private $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    /**
     * @param int $numberOfPages
     */
    public function setNumberOfPages(int $numberOfPages): void
    {
        $this->numberOfPages = $numberOfPages;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
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
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories): void
    {
        $this->categories = $categories;
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
            $review->setExclusiveBook($this);
        }

        return $this;
    }

    public function removeReview(BookReview $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getExclusiveBook() === $this) {
                $review->setExclusiveBook(null);
            }
        }

        return $this;
    }


}
