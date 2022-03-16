<?php

namespace App\Controller\api;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserApiController extends BaseRestController
{

    #[Get("/api/v1/users/{id}")]
    public function getUserById(
        User $user
    ):JsonResponse{
        return $this->jsonResponse(
           $user
        );
    }
    #[Get("/api/v1/users/{id}/reviews")]
    public function getUserReviews(
        User $user
    ):JsonResponse{
        return $this->jsonResponse(
            $user->getBookReviews()
        );
    }


}