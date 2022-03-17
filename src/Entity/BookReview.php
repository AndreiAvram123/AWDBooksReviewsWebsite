<?php

namespace App\Entity;

use App\Repository\BookReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: BookReviewRepository::class)]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
class BookReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;


    #[ORM\Column(type: 'boolean')]
    private bool $pending = true;

    #[ORM\Column(type: 'boolean')]
    private bool $declined = false;


    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;


    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: ReviewSection::class, cascade: ["persist","remove"], orphanRemoval: true)]
    private $sections;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private $frontImage;


    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: Comment::class, orphanRemoval: true)]
    #[MaxDepth(1)]
    private $comments;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookReviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    private $creator;

    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: PositiveRating::class, orphanRemoval: true)]
    private $positiveRatings;

    #[ORM\OneToMany(mappedBy: 'bookReview', targetEntity: NegativeRating::class, orphanRemoval: true)]
    private $negativeRatings;

    #[ORM\ManyToOne(targetEntity: ExclusiveBook::class, inversedBy: 'reviews')]
    private $exclusiveBook;

    #[ORM\ManyToOne(targetEntity: GoogleBook::class, inversedBy: 'reviews')]
    private $googleBook;




    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->creationDate = new \DateTime();
        $this->positiveRatings = new ArrayCollection();
        $this->negativeRatings = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getExclusiveBook(): ?Book
    {
        return $this->exclusiveBook;
    }

    public function setExclusiveBook(?Book $exclusiveBook): self
    {
        $this->exclusiveBook = $exclusiveBook;

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

    /**
     * @param ArrayCollection $sections
     */
    public function setSections(ArrayCollection $sections): void
    {
        $this->sections = $sections;
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


    public function hasUserRating(User $user): bool
    {
        $hasPositiveRating =  sizeof($this->positiveRatings->filter(function (UserRating $userRating) use ($user) {
            return $userRating->getCreator()->getId() === $user->getID();
        })) > 0;
        $hasNegativeRating =  sizeof($this->negativeRatings->filter(function (UserRating $userRating) use ($user) {
                return $userRating->getCreator()->getId() === $user->getID();
            })) > 0;

        return $hasNegativeRating === true || $hasPositiveRating === true;
    }

/**
 * @return Collection|PositiveRating[]
 */
public function getPositiveRatings(): Collection
{
    return $this->positiveRatings;
}

public function addPositiveRating(PositiveRating $positiveRating): self
{
    if (!$this->positiveRatings->contains($positiveRating)) {
        $this->positiveRatings[] = $positiveRating;
        $positiveRating->setBookReview($this);
    }

    return $this;
}

public function removePositiveRating(PositiveRating $positiveRating): self
{
    if ($this->positiveRatings->removeElement($positiveRating)) {
        // set the owning side to null (unless already changed)
        if ($positiveRating->getBookReview() === $this) {
            $positiveRating->setBookReview(null);
        }
    }

    return $this;
}

/**
 * @return Collection|NegativeRating[]
 */
public function getNegativeRatings(): Collection
{
    return $this->negativeRatings;
}

public function addNegativeRating(NegativeRating $negativeRating): self
{
    if (!$this->negativeRatings->contains($negativeRating)) {
        $this->negativeRatings[] = $negativeRating;
        $negativeRating->setBookReview($this);
    }

    return $this;
}

public function removeNegativeRating(NegativeRating $negativeRating): self
{
    if ($this->negativeRatings->removeElement($negativeRating)) {
        // set the owning side to null (unless already changed)
        if ($negativeRating->getBookReview() === $this) {
            $negativeRating->setBookReview(null);
        }
    }

    return $this;
}


    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGoogleBook(): ?GoogleBook
    {
        return $this->googleBook;
    }

    public function setGoogleBook(?GoogleBook $googleBook): self
    {
        $this->googleBook = $googleBook;

        return $this;
    }



}
