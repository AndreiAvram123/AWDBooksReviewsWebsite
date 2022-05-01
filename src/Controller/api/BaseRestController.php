<?php

namespace App\Controller\api;

use App\Entity\BookReview;
use App\Entity\User;
use App\Jwt\JWTPayload;
use App\Repository\UserRepository;
use App\ResponseModels\ErrorWrapper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Util\Exception;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function PHPUnit\Framework\throwException;

#[View(serializerEnableMaxDepthChecks: true)]
class BaseRestController extends AbstractFOSRestController
{
    protected Serializer $serializer;
    public function __construct(
        protected ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorageInterface,
        private JWTTokenManagerInterface $jwtManager,
        private UserRepository $userRepository
    )
    {
        $this->serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->build();
    }

    /**
     * @throws JWTDecodeFailureException
     */
    protected function getJWTPayload():JWTPayload{
        $payload =  $this->jwtManager->decode($this->tokenStorageInterface->getToken());
       return new JWTPayload(
            email: $payload['email'],
            roles: $payload['roles']
        ) ;

    }

    protected function getAuthenticatedUser():User{
        $payload =  $this->jwtManager->decode($this->tokenStorageInterface->getToken());
        $user =$this->userRepository->findByEmail($payload['email']);
        if($user == null){
            throw new Exception("An Unknown user accessed a secured endpoint");
        }
        return $user;
    }


    protected function createTokenForUser(User $user):string{
        return $this->jwtManager->create($user);
    }

    protected function jsonResponse(
        $data, int $statusCode = Response::HTTP_OK
    ):JsonResponse{
        return  JsonResponse::fromJsonString(
            $this->serializer->serialize($data,'json'),
            status: $statusCode
        );
    }


    protected function errorResponse(string $error, int $status = Response::HTTP_NOT_ACCEPTABLE):JsonResponse{
        $errorResponse = new ErrorWrapper(
            error: $error
        );
        return  JsonResponse::fromJsonString(
            $this->serializer->serialize($errorResponse,'json'),status : $status);
    }
    protected function constraintViolationResponse(ConstraintViolationListInterface $violationList) : JsonResponse{
        return $this->json(
            [
                "errors" => $this->fromViolationListToErrorsArray($violationList)
            ],
            status:  Response::HTTP_BAD_REQUEST
        );
    }

    private function fromViolationListToErrorsArray(ConstraintViolationListInterface $violationList):array{
        $arrayErrors = [];
        /**
         * @var $validationError ConstraintViolation
         */
        foreach($violationList as  $validationError){
            $errorObject = new StdClass();
            $errorObject -> property = $validationError->getPropertyPath();
            $errorObject-> error = $validationError->getMessage();
            $arrayErrors[] = $errorObject;
        }
        return $arrayErrors;
    }

    protected function persistAndFlush($entity){
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($entity);
        $manager->flush();
    }
    protected function deleteEntity($entity){
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($entity);
        $manager->flush();
    }

}