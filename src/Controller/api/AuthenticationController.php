<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\RequestModels\CreateUserRequest;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AuthenticationController extends BaseRestController
{
    private const EMAIL_ALREADY_USED = "Email already used";
    private const USERNAME_ALREADY_USED = "Username already used";

    #[Post("/api/v1/register")]
    public function registerUser(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ):JsonResponse{
        /**
         * @var  CreateUserRequest $serializedData
         */
       $serializedData = $this->serializer->deserialize(
           data: $request->getContent(),
           type: CreateUserRequest::class,
           format: 'json'
       );
       $validationErrors = $this->validator->validate($serializedData);

       if(count($validationErrors) === 0){
           //check if user with this email exists
           if($userRepository->findByEmail(
               $serializedData->getEmail()
           ) !== null){
               return $this->notAcceptableResponse(
                   array(self::EMAIL_ALREADY_USED));
           }
           //check if user with this username exists
           if($userRepository->findByUsername(
                   $serializedData->getUsername()
               ) !== null){
               return $this->notAcceptableResponse(
                   array(self::USERNAME_ALREADY_USED));
           }

          $user = $this->persistUserFromRequestData(
              createUserRequest: $serializedData,
              passwordHasher: $passwordHasher
          );

           return $this->json(
               array(
                   "accessToken" => $this->createTokenForUser($user)
               ),
               status: Response::HTTP_CREATED
           );
       }else{
           return $this->constraintViolationResponse(
               $validationErrors
           );
       }
    }

    private function persistUserFromRequestData(
        CreateUserRequest $createUserRequest,
        UserPasswordHasherInterface $passwordHasher
    ):User{
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user -> setUsername($createUserRequest->getUsername());
        $user->setEmail($createUserRequest->getEmail());
        $user->setPassword(
            $passwordHasher->hashPassword($user, $createUserRequest->getPassword())
        );
        $entityManager->persist($user);
        $entityManager->flush($user);
        return $user;
    }
}