<?php

namespace App\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{

    protected function getManager(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
    protected function persistAndFlush($object){
        $this->getManager()->persist($object);
        $this->getManager()->flush();
    }
}