<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;


#[ORM\Entity]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
 class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $pending = true;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'boolean')]
    private $declined = false;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookReview::class, orphanRemoval: true)]
    private $bookReviews;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $googleBookID;

    #[ORM\ManyToMany(targetEntity: BookCategory::class, inversedBy: 'books')]
    private $categories;

    public function __construct()
    {
        $this->bookReviews = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getPending(): ?bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): self
    {
        $this->pending = $pending;

        return $this;
    }



    public function getDeclined(): ?bool
    {
        return $this->declined;
    }

    public function setDeclined(bool $declined): self
    {
        $this->declined = $declined;

        return $this;
    }

    /**
     * @return Collection|BookReview[]
     */
    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookRevy(BookReview $bookRevy): self
    {
        if (!$this->bookReviews->contains($bookRevy)) {
            $this->bookReviews[] = $bookRevy;
            $bookRevy->setBook($this);
        }

        return $this;
    }

    public function removeBookRevy(BookReview $bookRevy): self
    {
        if ($this->bookReviews->removeElement($bookRevy)) {
            // set the owning side to null (unless already changed)
            if ($bookRevy->getBook() === $this) {
                $bookRevy->setBook(null);
            }
        }

        return $this;
    }

    public function getGoogleBookID(): ?string
    {
        return $this->googleBookID;
    }

    public function setGoogleBookID(?string $googleBookID): self
    {
        $this->googleBookID = $googleBookID;

        return $this;
    }

    /**
     * @return Collection|BookCategory[]
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

}
