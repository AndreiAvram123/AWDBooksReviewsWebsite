<?php

namespace App\Repository;

use App\Entity\BookReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookReview[]    findAll()
 * @method BookReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookReview::class);
    }


    public function findPending():array{
        return $this->createQueryBuilder('br')
                   ->andWhere('br.declined = false')
                   ->andWhere('br.pending = true')
                   ->getQuery()
                   ->getResult();
    }

    public function findAvailableToUsers():array{
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = false')
            ->getQuery()
            ->getResult();
    }

    public function findAllByTitle(string $query):array{
        return $this->createQueryBuilder('br')
            ->andWhere('LOWER(br.title) LIKE LOWER(:title)')
            ->setParameter('title','%'.$query.'%')
            ->getQuery()
            ->getResult();
    }

}
