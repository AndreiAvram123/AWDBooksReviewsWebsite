<?php

namespace App\Repository;

use App\Entity\GoogleBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoogleBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleBook[]    findAll()
 * @method GoogleBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleBooksLocalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleBook::class);
    }

    public function findByGoogleID(string $googleID):?GoogleBook{
        return $this->createQueryBuilder('b')
                    ->where('b.googleBookID = :googleBookID')
                   ->setParameter('googleBookID',$googleID)
                   ->setMaxResults(1)
                   ->getQuery()
                   ->getOneOrNullResult();
    }
}
