<?php

namespace App\Repository;

use App\Controller\BookReviewController;
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
                   ->orderBy('br.creationDate','DESC')
                   ->getQuery()
                   ->getResult();
    }

    public function findPubliclyAvailable(int $page = 1):array{
        $offset = BookReviewController::$itemsPerPage * ($page-1);
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = false')
            ->setFirstResult($offset)
            ->setMaxResults(BookReviewController::$itemsPerPage)
            ->orderBy('br.creationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countPubliclyAvailable():int{
        return $this->createQueryBuilder('br')
            ->select('count(br.id)')
            ->andWhere('br.pending = false')
            ->andWhere('br.declined = false')
            ->getQuery()
            ->getSingleScalarResult();
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
