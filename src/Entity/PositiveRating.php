<?php

namespace App\Entity;

use App\Repository\PositiveRatingRepository;
use Doctrine\ORM\Mapping as ORM;

//marker class
#[ORM\Entity(repositoryClass: PositiveRatingRepository::class)]
class PositiveRating extends UserRating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    public function getId(): ?int
    {
        return $this->id;
    }
}
