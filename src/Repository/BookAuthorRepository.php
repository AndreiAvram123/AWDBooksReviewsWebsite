<?php

namespace App\Repository;

use App\Entity\BookAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookAuthor|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookAuthor|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookAuthor[]    findAll()
 * @method BookAuthor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookAuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookAuthor::class);
    }
}
