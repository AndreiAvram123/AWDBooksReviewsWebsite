<?php

namespace App\Controller\api;

use App\Entity\User;
use App\Jwt\RefreshTokenService;
use App\Repository\EmailValidationRepository;
use App\Repository\UserRepository;
use App\RequestModels\CreateUserRequest;
use App\ResponseModels\ErrorResponse;
use App\services\EmailService;
use Firebase\JWT\ExpiredException;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;


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
        UserPasswordHasherInterface $passwordHasher,
        EmailService $emailService
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
                return $this->errorResponse(self::EMAIL_ALREADY_USED);
            }
            //check if user with this username exists
            if($userRepository->findByUsername(
                    $serializedData->getUsername()
                ) !== null){
                return $this->errorResponse(self::USERNAME_ALREADY_USED);
            }

            $user = $this->persistUserFromRequestData(
                createUserRequest: $serializedData,
                passwordHasher: $passwordHasher
            );
            $emailService->sendConfirmationEmail(
                user: $user
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

    /**
     *
     * Get a new authentication token by providing a refresh token
     * @OA\Response (
     *     response=200,
     *     description="Return a new access token",
     *     @OA\JsonContent(type="object",
     *     @OA\Property(property="accessToken",type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9"))
     * )
     *
     * @OA\Parameter(
     *     name="refreshToken",
     *     in = "query",
     *     description="The refresh token provided on login",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name = "Authentication")
     */
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
            return  $this->errorResponse("Expired token");
        }catch (\Exception){
            return  $this->errorResponse("Not a valid refresh token");
        }

        $user = $userRepository->findByEmail(
            $decodedToken->getEmail()
        );
        if($user === null){
            return $this->errorResponse("Not a valid refresh token");
        }
        return $this->jsonResponse(
            array(
                'accessToken'=> $JWTTokenManager->create($user)
            )
        );
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