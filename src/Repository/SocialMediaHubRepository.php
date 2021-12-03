<?php

namespace App\Repository;

use App\Entity\SocialMediaHub;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SocialMediaHub|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialMediaHub|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialMediaHub[]    findAll()
 * @method SocialMediaHub[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialMediaHubRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialMediaHub::class);
    }

}
