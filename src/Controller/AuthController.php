<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationType;
use App\Jwt\RefreshTokenService;
use App\ResponseModels\ErrorResponse;
use App\services\EmailService;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    #[Route("/verify")]
    public function validateEmail(
        Request $request,
        EmailService $emailService
    ):Response{
        $uuid = $request->query->get('uuid');

        $validationError = $emailService->validateUuid($uuid);
        if($validationError === null){
            return $this->json(data: new StdClass());
        }
        return $this->json(
            new ErrorResponse($validationError),
            status: Response::HTTP_GONE
        );
    }

}
