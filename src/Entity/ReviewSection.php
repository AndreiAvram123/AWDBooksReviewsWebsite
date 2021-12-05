<?php

namespace App\Entity;

use App\Repository\ReviewSectionRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

#[ORM\Entity(repositoryClass: ReviewSectionRepository::class)]
class ReviewSection implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $text;

    #[ORM\Column(type: 'text', nullable: true)]
    private $heading;

    #[ORM\ManyToOne(targetEntity: BookReview::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private $bookReview;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getHeading(): ?string
    {
        return $this->heading;
    }

    public function setHeading(string $heading): self
    {
        $this->heading = $heading;

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

    #[ArrayShape(['heading' => "", 'text' => ""])] public function jsonSerialize(): array
    {
        return [
            'heading'=>$this->heading,
            'text'=>$this->text
        ];
    }
}
