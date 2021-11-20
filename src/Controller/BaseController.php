<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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

    protected function provideAuthenticatedUser():User{
        $genericUser = $this->getUser();
        if(is_null($genericUser)){
            //if this is reached then there is a security flaw
        }
        return $this->getManager()
            ->getRepository(User::class)
            ->findByEmail($genericUser->getUserIdentifier());
    }
}