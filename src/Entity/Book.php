<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'integer')]
    private int $numberOfPages;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: BookReview::class, orphanRemoval: true)]
    private  $bookReviews;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $pending;

    #[ORM\ManyToOne(targetEntity: BookAuthor::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $image;

    #[ORM\ManyToOne(targetEntity: BookCategory::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[Pure] public function __construct()
    {
        $this->bookReviews = new ArrayCollection();
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

    public function getNumberOfPages(): ?int
    {
        return $this->numberOfPages;
    }

    public function setNumberOfPages(int $numberOfPages): self
    {
        $this->numberOfPages = $numberOfPages;

        return $this;
    }

    /**
     * @return Collection|BookReview[]
     */
    public function getBookReviews(): Collection
    {
        return $this->bookReviews;
    }

    public function addBookReview(BookReview $bookReview): self
    {
        if (!$this->bookReviews->contains($bookReview)) {
            $this->bookReviews[] = $bookReview;
            $bookReview->setBook($this);
        }

        return $this;
    }

    public function removeBookReview(BookReview $bookReview): self
    {
        if ($this->bookReviews->removeElement($bookReview)) {
            // set the owning side to null (unless already changed)
            if ($bookReview->getBook() === $this) {
                $bookReview->setBook(null);
            }
        }

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

    public function jsonSerialize()
    {

    }

    public function getAuthor(): ?BookAuthor
    {
        return $this->author;
    }

    public function setAuthor(?BookAuthor $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?BookCategory
    {
        return $this->category;
    }

    public function setCategory(?BookCategory $category): self
    {
        $this->category = $category;

        return $this;
    }
}
