<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends BaseController
{
    #[Route('/login', name: 'login_path')]
    public function index(): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class,$user);;
        return $this->renderForm('auth/login.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/register', name: 'register_path')]
    public function register (Request $request):Response{
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $user = $form->getData();
            $this->persistAndFlush($user);
            return $this->redirectToRoute('login_path');
        }
        return $this->renderForm('auth/registration.html.twig', [
            'form' => $form
        ]);
    }

}
