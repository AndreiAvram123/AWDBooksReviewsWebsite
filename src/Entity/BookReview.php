<?php

namespace App\Entity;

use App\Repository\BookReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;

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

    #[ORM\Column(type: 'text')]
    #[NotB(min: 20 , minMessage: "Your summary is too short")]
    private string $summary;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    #[ORM\Column(type: 'boolean')]
    private bool $pending = true;

    #[ORM\Column(type: 'boolean')]
    private bool $declined = false;


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
}
