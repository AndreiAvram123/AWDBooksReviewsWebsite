<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;


#[ORM\MappedSuperclass]
#[ExclusionPolicy(ExclusionPolicy::NONE)]
abstract class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $id;


    #[ORM\Column(type: 'boolean', nullable: false)]
    private $pending = true;


    #[ORM\Column(type: 'string', nullable: false)]
    private string $title;

    #[ORM\Column(type: 'boolean')]
    private $declined = false;




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

    public function setDeclined(bool $declined): self
    {
        $this->declined = $declined;

        return $this;
    }

}
