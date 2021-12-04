<?php

namespace App\Repository;

use App\Entity\NegativeRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NegativeRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method NegativeRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method NegativeRating[]    findAll()
 * @method NegativeRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NegativeRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NegativeRating::class);
    }

    // /**
    //  * @return NegativeRating[] Returns an array of NegativeRating objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NegativeRating
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
