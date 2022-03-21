<?php

namespace App\Repository;

use App\Entity\EmailValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EmailValidation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailValidation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailValidation[]    findAll()
 * @method EmailValidation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailValidation::class);
    }

    public function findByUuid(string $uuid):?EmailValidation{
        return $this->createQueryBuilder('ev')
               ->where('ev.uuid = :uuid')
                ->setParameter('uuid', $uuid)
               ->getQuery()
               ->getOneOrNullResult();

    }
}
