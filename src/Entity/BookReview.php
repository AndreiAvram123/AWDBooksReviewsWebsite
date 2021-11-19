<?php

namespace App\Entity;

use App\Repository\BookReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: BookReviewRepository::class)]
class BookReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Book::class, inversedBy: 'bookReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    #[ORM\Column(type: 'boolean')]
    private bool $pending = true;

    #[ORM\Column(type: 'boolean')]
    private bool $declined = false;


    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $estimatedReadTime;

    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: ReviewSection::class, orphanRemoval: true)]
    private $sections;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $frontImage;

    #[ORM\OneToOne(targetEntity: Rating::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $rating;

    #[Pure] public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->sections = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

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

    public function setDeclined(?bool $declined): self
    {
        $this->declined = $declined;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setBookReview($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getBookReview() === $this) {
                $image->setBookReview(null);
            }
        }

        return $this;
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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getEstimatedReadTime(): ?int
    {
        return $this->estimatedReadTime;
    }

    public function setEstimatedReadTime(?int $estimatedReadTime): self
    {
        $this->estimatedReadTime = $estimatedReadTime;

        return $this;
    }

    /**
     * @return Collection|ReviewSection[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(ReviewSection $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setBookReview($this);
        }

        return $this;
    }

    public function removeSection(ReviewSection $section): self
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getBookReview() === $this) {
                $section->setBookReview(null);
            }
        }

        return $this;
    }

    public function getFrontImage(): ?Image
    {
        return $this->frontImage;
    }

    public function setFrontImage(?Image $frontImage): self
    {
        $this->frontImage = $frontImage;

        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function setRating(Rating $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
