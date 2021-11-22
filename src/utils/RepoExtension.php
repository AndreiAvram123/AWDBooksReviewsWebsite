<?php

namespace App\utils;

use Doctrine\Persistence\ManagerRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RepoExtension extends AbstractExtension
{
    public function __construct(private ManagerRegistry $managerRegistry)
    {

    }

    public function getFilters()
    {
        return [
            new TwigFilter('entities',[$this,'getEntities'])
        ];
    }
    public function  getEntities($entityClass){
        $this->managerRegistry->getRepository($entityClass::class)->findAll();
    }
}