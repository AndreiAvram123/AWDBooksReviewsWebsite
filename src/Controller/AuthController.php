<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends BaseController
{
    #[Route('/login', name: 'login_path')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class,$user);
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->renderForm('auth/login.html.twig', [
            'form' => $form,
            'invalidCredentials'=> $error != null
        ]);
    }

    #[Route('/register', name: 'register_path')]
    public function register (
        Request $request,
        UserPasswordHasherInterface $hasher
    ):Response{
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            /** @var $user User**/
            $user = $form->getData();
            $hashedPassword = $hasher->hashPassword(user : $user ,
                plainPassword: $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $this->persistAndFlush($user);
            return $this->redirectToRoute('login_path');
        }
        return $this->renderForm('auth/registration.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/logout", name: "logout")]
    public function logout(){

    }


}
