<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;




#[ORM\MappedSuperclass]
class UserRating
{

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userRatings')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;


    #[ORM\ManyToOne(targetEntity: BookReview::class, inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private $bookReview;


    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

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
