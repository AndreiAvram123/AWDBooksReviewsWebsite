<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findByEmail($email):?User{
         return $this->createQueryBuilder('u')
               ->andWhere('u.email = :email')
               ->setParameter('email',$email)
               ->getQuery()
              ->getOneOrNullResult();
    }
    public function findByUsernameQuery(string $query):array{
        return $this->createQueryBuilder('u')
                ->andWhere('LOWER(u.username) LIKE LOWER(:query)')
                ->setParameter('query','%'.$query.'%')
                ->getQuery()
                ->getResult();
    }

}
