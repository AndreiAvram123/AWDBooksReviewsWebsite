<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'user_profile')]
    public function getUserById(User $user): Response
    {
        return $this->render('user/user_profile.twig', [
           'user' => $user
        ]);
    }
}
