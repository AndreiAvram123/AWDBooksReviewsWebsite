<?php

namespace App\Repository;

use App\Entity\ReviewSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReviewSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReviewSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReviewSection[]    findAll()
 * @method ReviewSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReviewSection::class);
    }

    // /**
    //  * @return ReviewSection[] Returns an array of ReviewSection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReviewSection
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
