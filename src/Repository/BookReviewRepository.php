<?php

namespace App\Repository;

use App\Controller\BookReviewController;
use App\Entity\BookReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        return $this->createPubliclyAvailableQueryBuilder()
            ->setFirstResult($offset)
            ->setMaxResults(BookReviewController::$itemsPerPage)
            ->orderBy('br.creationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFeaturedReviews():array{
        $qb = $this->createPubliclyAvailableQueryBuilder();
        return $qb-> andWhere('SIZE(br.positiveRatings) > SIZE(br.negativeRatings)')
             ->orderBy('SIZE(br.positiveRatings)','DESC')
             ->getQuery()
             ->getResult();
    }

    //find the posts that have the number of positive reviews higher than the number fo negative reviews
    private function createPubliclyAvailableQueryBuilder():QueryBuilder{
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = false') ;
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
