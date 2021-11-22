<?php

namespace App\Entity;

use App\Repository\UserRatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRatingRepository::class)]
class UserRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    #[ORM\Column(type: 'boolean')]
    private $isPositiveRating;

    #[ORM\ManyToOne(targetEntity: BookReview::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private $bookReview;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsPositiveRating(): ?bool
    {
        return $this->isPositiveRating;
    }

    public function setIsPositiveRating(bool $isPositiveRating): self
    {
        $this->isPositiveRating = $isPositiveRating;

        return $this;
    }

    public function getBookReview(): ?BookReview
    {
        return $this->bookReview;
    }

    public function setBookReview(?BookReview $bookReview): self
    {
        $this->bookReview = $bookReview;

        return $this;
    }
}
