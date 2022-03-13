<?php

namespace App\Controller\api;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserApiController extends BaseRestController
{

    #[Get("/api/users/{id}")]
    public function getUserById(
        User $user
    ):JsonResponse{
        return $this->jsonResponse(
            $this->serializer->serialize(
                data: $user,
                format: 'json'
            )
        );
    }
}