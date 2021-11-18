<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'get_user_by_id')]
    public function getUserById(User $user): Response
    {
        return $this->render('user/user.twig', [
           'user' => $user
        ]);
    }
}
