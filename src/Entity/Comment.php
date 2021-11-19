<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;

    #[ORM\Column(type: 'text')]
    private $summary;

    #[ORM\ManyToOne(targetEntity: BookReview::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $bookReview;

    #[ORM\Column(type: 'datetime')]
    private $creationDate;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'subcomments')]
    private $repliedComment;

    #[ORM\OneToMany(mappedBy: 'repliedComment', targetEntity: Comment::class)]
    private $subcomments;

    public function __construct()
    {
        $this->subcomments = new ArrayCollection();
    }

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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getRepliedComment(): ?self
    {
        return $this->repliedComment;
    }

    public function setRepliedComment(?self $repliedComment): self
    {
        $this->repliedComment = $repliedComment;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSubcomments(): Collection
    {
        return $this->subcomments;
    }

    public function addSubcomment(self $subcomment): self
    {
        if (!$this->subcomments->contains($subcomment)) {
            $this->subcomments[] = $subcomment;
            $subcomment->setRepliedComment($this);
        }

        return $this;
    }

    public function removeSubcomment(self $subcomment): self
    {
        if ($this->subcomments->removeElement($subcomment)) {
            // set the owning side to null (unless already changed)
            if ($subcomment->getRepliedComment() === $this) {
                $subcomment->setRepliedComment(null);
            }
        }

        return $this;
    }
}
