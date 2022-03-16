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


    private function createPendingQB():QueryBuilder{
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = true');
    }

    public function findPending():array{
        return   $this->createPendingQB()
            ->orderBy('br.creationDate','DESC')
            ->getQuery()
            ->getResult();
    }

    public function countPending():int{
        $qb = $this->createPendingQB();
        return $qb -> select(
             $qb->expr()->count('br.id')
           )->getQuery()->getSingleScalarResult();
    }



    public function findPubliclyAvailable(int $page = 1):array{
        $offset = BookReviewController::$itemsPerPage * ($page-1);
        return $this->createPubliclyAvailableQB()
            ->setFirstResult($offset)
            ->setMaxResults(BookReviewController::$itemsPerPage)
            ->orderBy('br.creationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFeaturedReviews():array{
        return $this->createPubliclyAvailableQB()
            ->andWhere('SIZE(br.positiveRatings) > SIZE(br.negativeRatings)')
            ->orderBy('SIZE(br.positiveRatings)','DESC')
            ->addOrderBy('br.creationDate','DESC')
            ->getQuery()
            ->getResult();
    }

    //find the posts that have the number of positive reviews higher than the number fo negative reviews
    private function createPubliclyAvailableQB():QueryBuilder{
        return $this->createQueryBuilder('br')
            ->andWhere('br.declined = false')
            ->andWhere('br.pending = false') ;
    }



    public function countPubliclyAvailable():int{
        $qb = $this->createQueryBuilder('br');
        return $qb->select($qb->expr()->count('br.id'))
            ->andWhere('br.pending = false')
            ->andWhere('br.declined = false')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findByTitle(string $query):array{
        if($query === ""){
            return [];
        }
        $qb = $this->createPubliclyAvailableQB();
        return $qb->andWhere(
                $qb->expr()->like(
                    $qb->expr()->lower('br.title'),
                    $qb->expr()->lower(':title')
                )
            )
            ->setParameter('title','%'.$query.'%')
            ->setMaxResults(100)
             ->orderBy('br.creationDate','DESC')
            ->getQuery()
            ->getResult();
    }

}
