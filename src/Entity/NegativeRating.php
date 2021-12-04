<?php

namespace App\Entity;

use App\Repository\NegativeRatingRepository;
use Doctrine\ORM\Mapping as ORM;

//marker class
#[ORM\Entity(repositoryClass: NegativeRatingRepository::class)]
class NegativeRating extends UserRating
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
