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
    static int $itemsPerPage = 10;

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

    public function findPubliclyAvailable(int $page = 1):array{
        $offset = self::$itemsPerPage * ($page-1);
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = false')
            ->setFirstResult($offset)
            ->setMaxResults(self::$itemsPerPage)
            ->getQuery()
            ->getResult();
    }

    public function findAllByTitle(string $query):array{
        return $this->createQueryBuilder('br')
            ->andWhere('LOWER(br.title) LIKE LOWER(:title)')
            ->setParameter('title','%'.$query.'%')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult();
    }

}
