<?php

namespace App\Controller\api;

use App\Entity\BookReview;
use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[View(serializerEnableMaxDepthChecks: true)]
class BaseRestController extends AbstractFOSRestController
{
    protected Serializer $serializer;
    public function __construct(
        protected ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorageInterface,
        private JWTTokenManagerInterface $jwtManager
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
    protected function getEmailFromToken(){
        return $this->jwtManager->decode($this->tokenStorageInterface->getToken())['email'];
    }
    protected function createTokenForUser(User $user):string{
        return $this->jwtManager->create($user);
    }

    protected function jsonResponse( $data):JsonResponse{
        return  JsonResponse::fromJsonString($this->serializer->serialize($data,'json'));
    }


    protected function notAcceptableResponse(array $errors):JsonResponse{
        return $this->json(
            [
                "errors" =>$errors
            ],
            status: Response::HTTP_NOT_ACCEPTABLE
        );
    }
    protected function constraintViolationResponse(ConstraintViolationListInterface $violationList) : JsonResponse{
        return $this->json(
            [
                "errors" => $this->fromViolationListToErrorsArray($violationList)
            ],
            status: Response::HTTP_NOT_ACCEPTABLE
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


}