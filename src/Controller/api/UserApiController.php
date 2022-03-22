<?php

namespace App\Controller\api;

use App\Entity\User;
use App\RequestModels\CreateBookReviewModel;
use App\RequestModels\UpdateUserModel;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
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
use Symfony\Component\HttpFoundation\Request;

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


    #[Patch("/api/v1/users/{id}")]
    public function updateUserByID(
        User $user,
        Request $request
    ):JsonResponse{
        /** @var UpdateUserModel $updateModel */
        $updateModel  = $this->serializer->deserialize(
            data: $request->getContent(),
            type: UpdateUserModel::class,
            format: 'json');

        $validationErrors =  $this->validator->validate($updateModel);
        if(count($validationErrors) === 0){
            $this->updateUser($updateModel, $user);
            $userValidationErrors = $this->validator->validate($user);
             if(count($userValidationErrors) > 0 ){
                return $this->constraintViolationResponse($userValidationErrors);
            }
             $this->persistAndFlush($user);
             return $this->jsonResponse($user);
        }else{
            return $this->constraintViolationResponse(
                $validationErrors
            );
        }
    }
    private function updateUser(
        UpdateUserModel $updateUserModel,
        User &$user
    ){
        $user ->setEmail($updateUserModel->getEmail());
        $user->setNickname($updateUserModel->getNickname());
        $user->setUsername($updateUserModel->getUsername());
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