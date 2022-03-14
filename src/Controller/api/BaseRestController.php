<?php

namespace App\Controller\api;

use App\Entity\BookReview;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseRestController extends AbstractFOSRestController
{
    protected Serializer $serializer;
    public function __construct(

        protected ValidatorInterface $validator
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


    protected function jsonResponse( $data):JsonResponse{
        return  JsonResponse::fromJsonString($this->serializer->serialize($data,'json'));
    }

    protected function isDataValid($data) : bool{
        $errors = $this->validator->validate($data);
        return count($errors) === 0;
    }
}