<?php

namespace App\Controller\api;

use App\Entity\User;
use App\RequestModels\CreateBookReviewModel;
use App\RequestModels\UpdateUserModel;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Put;
use OpenApi\Annotations\Property;
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


    /**
     *  Update a user with the specified id
     *
     * @Response(
     *     description="Update a user with the specified id",
     *     response=200,
     *     @JsonContent(
     *       ref= @Model(type= User::class)
     * )
     *
     * )
     * @Response(
     *     description="Invalid update data. Values already used ",
     *     response = 400,
     *     @JsonContent(
     *     type = "object",
     *     @Property (property="errors", type="array",
     *      @Items(type="object",
     *       @Property (property="property", type="string", example="The username is already taken")))
     * )
     * )
     *  @Parameter(
     *     name="id",
     *     in = "path",
     *     @Schema(type="integer")
     * )
     * @Tag(name = "Users")
     * @Security(name="Bearer")
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    #[Patch("/api/v1/users/{id}")]
    public function updateUserByID(
        Request $request,
        User $user
    ):JsonResponse{
        //allow modification of user data only if the authentication user is the same
        //as in token
        if($this->getJWTPayload()->getEmail() !== $user->getEmail()){
            return $this->errorResponse(
                error : "Not authorized for this operations",
                status: \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN
            );
        }

        /** @var UpdateUserModel $updateModel */
        $updateModel  = $this->serializer->deserialize(
            data: $request->getContent(),
            type: UpdateUserModel::class,
            format: 'json');

            $this->updateUser($updateModel, $user);
            $userValidationErrors = $this->validator->validate($user);
             if(count($userValidationErrors) > 0 ){
                return $this->constraintViolationResponse($userValidationErrors);
            }
             $this->persistAndFlush($user);
             return $this->jsonResponse($user);
        }

    private function updateUser(
        UpdateUserModel $updateUserModel,
        User &$user
    ){
        $email = $updateUserModel->getEmail();
        $nickname = $updateUserModel->getNickname();
        $username = $updateUserModel->getUsername();
        $password = $updateUserModel->getPassword();
        if($email !== null){
            $user ->setEmail($email);
        }
        if($nickname !== null){
            $user ->setNickname($nickname);
        }
        if($username !== null){
            $user ->setUsername($username);
        }
        if($password !== null){
            $user->setPassword($password);
        }
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