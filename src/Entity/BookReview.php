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


    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: Comment::class, orphanRemoval: true)]
    private $comments;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookReviews')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: UserRating::class, orphanRemoval: true)]
    private $ratings;


    #[Pure] public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->creationDate = new \DateTime();
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


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setBookReview($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBookReview() === $this) {
                $comment->setBookReview(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRating[]
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(UserRating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings[] = $rating;
            $rating->setBookReview($this);
        }

        return $this;
    }

    public function removeRating(UserRating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getBookReview() === $this) {
                $rating->setBookReview(null);
            }
        }

        return $this;
    }

    public function getPositiveRatings(): ArrayCollection
    {
        return $this->ratings->filter(function ( UserRating $rating){
            return $rating->getIsPositiveRating() == true;
        });
    }
    public function getNegativeRatings(): ArrayCollection
    {
        return $this->ratings->filter(function ( UserRating $rating){
            return $rating->getIsPositiveRating() == false;
        });
    }

    public function hasUserRating(User $user): bool
    {
        $filteredArray =  $this->ratings->filter(function (UserRating $userRating) use ($user) {
            return $userRating->getCreator()->getId() === $user->getID();
        });
        return sizeof($filteredArray) > 0;
    }

}
