<?php

namespace App\utils\entities;

use App\Entity\BookReview;
use App\Entity\NegativeRating;
use App\Entity\PositiveRating;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Security;


class RatingUtils
{

    public function __construct(
        private Security $security,
        private EntityManagerInterface $manager
    ){

    }


    public function addRatingToBookReview(bool $isPositive, BookReview $bookReview): void
    {
        if($isPositive === true){
            $rating = $this->createPositiveRating();
            $this->manager->persist($rating);
            $bookReview->addPositiveRating($rating);
        }else{
            $rating = $this->createNegativeRating();
            $this->manager->persist($rating);
            $bookReview->addNegativeRating($rating);
        }
    }

    private function createPositiveRating():PositiveRating{
        $rating = new PositiveRating();
        /** @var User $user */
        $user = $this->security->getUser();
        $rating->setCreator($user);
        return  $rating;
    }
    private function createNegativeRating():NegativeRating{
        $rating = new NegativeRating();
        /** @var User $user */
        $user = $this->security->getUser();
        $rating->setCreator($user);
        return  $rating;
    }
}