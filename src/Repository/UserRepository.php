<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUsernameQuery(string $query):array{
        if($query === ""){
            return [];
        }
        $qb = $this->createQueryBuilder('u');
        return $qb->andWhere(
              $qb->expr()->like(
                  $qb->expr()->lower('u.username'),
                  $qb->expr()->lower(':query'),
              ))->setParameter('query','%'.$query.'%')
                 ->setMaxResults(100)
                ->getQuery()
                ->getResult();
    }
    public function findByEmail(string $email):User{
        $qb = $this->createQueryBuilder('u');
        return $qb->andWhere('u.email = :email')
               ->setParameter('email',$email)
               ->getQuery()
                ->getSingleResult();
    }
}
