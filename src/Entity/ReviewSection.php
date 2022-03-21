<?php

namespace App\Entity;

use App\Repository\ReviewSectionRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ReviewSectionRepository::class)]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
class ReviewSection implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Exclude]
    private $id;

    #[ORM\Column(type: 'text')]
    #[NotBlank]
    private string $text;

    #[ORM\Column(type: 'text', nullable: true)]
    #[NotBlank]
    private string $heading;

    #[ORM\ManyToOne(targetEntity: BookReview::class, cascade: ['persist'], inversedBy: 'sections')]
    #[Exclude]
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
