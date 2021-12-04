<?php

namespace App\Repository;

use App\Entity\PositiveRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PositiveRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method PositiveRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method PositiveRating[]    findAll()
 * @method PositiveRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PositiveRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PositiveRating::class);
    }

    // /**
    //  * @return PositiveRating[] Returns an array of PositiveRating objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PositiveRating
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
