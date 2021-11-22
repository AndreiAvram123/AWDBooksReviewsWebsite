<?php

namespace App\Repository;

use App\Controller\BookController;
use App\Controller\BookReviewController;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
        return $this->createQueryBuilder('b')
              ->where('LOWER(b.title) LIKE LOWER(:title)')
              ->setParameter('title', '%'.$title.'%')
               ->setMaxResults(100)
              ->getQuery()
              ->getResult();

    }
    public function countPubliclyAvailable():int{
      return $this->createQueryBuilder('b')
             ->select('count(b.id)')
             ->andWhere('b.pending = false')
             ->andWhere('b.declined = false')
             ->getQuery()
             ->getSingleScalarResult();

    }

    public function findPubliclyAvailable(int $page = 1):array{
        $offset = BookController::$itemsPerPage * ($page-1);
        return $this->createQueryBuilder('b')
            ->andWhere('b.pending = false')
            ->andWhere('b.pending = false')
            ->setFirstResult($offset)
            ->setMaxResults(BookReviewController::$itemsPerPage)
            ->getQuery()
            ->getResult();
    }
}
