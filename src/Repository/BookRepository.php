<?php

namespace App\Repository;

use App\Controller\BookController;
use App\Controller\BookReviewController;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findByTitle(string $title):array{
        $qb= $this->createPubliclyAvailableQB();
        return $qb->where(
            $qb->expr()->like(
                $qb->expr()->lower('b.title'),
                $qb->expr()->lower(':title')
            ))->setParameter('title', '%'.$title.'%')
               ->setMaxResults(100)
              ->getQuery()
              ->getResult();
    }

    public function countPubliclyAvailable():int{
     $qb = $this->createPubliclyAvailableQB();
      return $qb->select(
          $qb->expr()->count('b.id')
      )->getQuery()->getSingleScalarResult();
    }


    public function findPending():array{
        return $this->createQueryBuilder('b')
               ->andWhere('b.pending = true')
               ->andWhere('b.declined = false')
               ->getQuery()
               ->getResult();
    }


    public function findPubliclyAvailable(int $page = 1): array
    {
        $offset = BookController::$itemsPerPage * ($page-1);
        return $this->createPubliclyAvailableQB()
            ->setFirstResult($offset)
            ->setMaxResults(BookReviewController::$itemsPerPage)
            ->getQuery()
            ->getResult();
    }

    public function createPubliclyAvailableQB(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.pending = true')
            ->andWhere('b.declined = false');
    }



}
