<?php

namespace App\Controller\api;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\BookReview;

class UserApiController extends BaseRestController
{

    /**
     * Get the user with the specified id
     * @Response(
     *     description="Get the user with the specified id",
     *     response= 200,
     *     @JsonContent(ref= @Model(type= User::class))
     * )
     * @Parameter(
     *     name="id",
     *     in = "path",
     *     @Schema(type="integer")
     * )
     * @Tag(name="Users")
     * @Security(name="Bearer")
     * @param User $user
     * @return JsonResponse
     */
    #[Get("/api/v1/users/{id}")]
    public function getUserById(
        User $user
    ):JsonResponse{
        return $this->jsonResponse(
           $user
        );
    }

    /**
     * Get reviews for user with the given id
     * @Response(
     *     description="Get reviews for the user with the given id ",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref = @Model(type= BookReview::class))
     *  )
     * )
     *  @Parameter(
     *     name="id",
     *     in = "path",
     *     @Schema(type="integer")
     * )
     * @Tag(name="Users")
     * @Security(name="Bearer")
     * @param User $user
     * @return JsonResponse
     */
    #[Get("/api/v1/users/{id}/reviews")]
    public function getUserReviews(
        User $user
    ):JsonResponse{
        return $this->jsonResponse(
            $user->getBookReviews()
        );
    }


}