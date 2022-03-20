<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Jwt\RefreshTokenService;
use App\Repository\UserRepository;
use App\RequestModels\CreateUserRequest;
use Firebase\JWT\ExpiredException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Tag;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


class AuthApiController extends BaseRestController
{
    private const EMAIL_ALREADY_USED = "Email already used";
    private const USERNAME_ALREADY_USED = "Username already used";

    /**
     *
     * Register a user
     *
     * @OA\Response(
     *     response=201,
     *     description="Returns 201 on successful registration",
     *     @OA\JsonContent(type="object")
     * )
     * @OA\Tag(name="Authentication")
     * @OA\RequestBody(
     *     description="registration data",
     *     @Model(type=CreateUserRequest::class )
     * )
     */
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
                return $this->notAcceptableResponse(self::EMAIL_ALREADY_USED);
            }
            //check if user with this username exists
            if($userRepository->findByUsername(
                    $serializedData->getUsername()
                ) !== null){
                return $this->notAcceptableResponse(self::USERNAME_ALREADY_USED);
            }

            $user = $this->persistUserFromRequestData(
                createUserRequest: $serializedData,
                passwordHasher: $passwordHasher
            );

            return $this->jsonResponse(
                data: new StdClass(),
                statusCode: Response::HTTP_CREATED
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

    #[Get("/api/v1/token")]
    #[QueryParam(
        name: "refreshToken",
        strict: true,
        allowBlank: false
    )]
    public function getNewAccessToken(
        ParamFetcher $paramFetcher,
        RefreshTokenService $refreshTokenService,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager
    ):JsonResponse{
        try {
            $decodedToken  = $refreshTokenService->getDecodedJWT($paramFetcher->get('refreshToken'));
        }catch (ExpiredException){
            return  $this->notAcceptableResponse("Expired token");
        }catch (\Exception){
            return  $this->notAcceptableResponse("Not a valid refresh token");
        }

        $user = $userRepository->findByEmail(
            $decodedToken->getEmail()
        );
        if($user === null){
            return $this->notAcceptableResponse("Not a valid refresh token");
        }
        return $this->jsonResponse(
            array(
                'accessToken'=> $JWTTokenManager->create($user)
            )
        );
    }

}