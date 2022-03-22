<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Repository\EmailValidationRepository;

use App\services\EmailService;
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
        UserPasswordHasherInterface $hasher
    ):Response{
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        if($this->canAccessFormData($form)){
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

    /**
     * The configuration will intersect this path
     * The method needs to be declared but does not need to return anything
     */
    #[Route("/logout", name: "logout")]
    public function logout(){

    }
    #[Route("/verification")]
    public function validateEmail(
        Request $request,
        EmailService $emailService,
        EmailValidationRepository $emailValidationRepository
    ):Response{
        $uuid = $request->query->get('uuid');

        $validationError = null;
        if($uuid === null || $uuid === "") {
            $validationError = "Invalid link";
        }
        $validation = $emailValidationRepository->findByUuid($uuid);

        if($validation === null){
            $validationError = "Invalid Link";
        }else{
            if($validation->getExpirationDate() < new \DateTime()){
                $validationError = "Oops...The link has expired. But we've sent a new one to your email";
                $emailService->removeExpiredVerification($validation);
                $emailService->sendConfirmationEmail($validation->getUser());
            }else{
                $emailService->setEmailValidated($validation);
            }
        }

        return $this->render(
            'auth/verification_response.twig',
            [
                'validationError' => $validationError
            ]
        );
    }

}
