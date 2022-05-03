<?php

namespace App\Controller\api;


use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\UserRepository;
use App\Entity\Comment;
use App\RequestModels\CreateBookReviewModel;
use App\ResponseModels\ErrorWrapper;
use App\utils\aws\AwsImageUtils;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use OpenApi\Annotations\Property;

class BookReviewApiController extends BaseRestController
{

    private const ERROR_BOOK_NOT_FOUND = "The book with this id was not found";
    private const INVALID_BASE64_IMAGE = "Invalid base 64 image";

    /**
     * @Response(
     *     description="Successfully returned book reviews by page",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref= @Model(type=BookReview::class))
     * )
     * )
     * @Parameter(
     *     name="page",
     *     in = "query",
     *     @Schema(type="integer")
     *  )
     * @Tag(name="Book Reviews")
     * @Security(name="Bearer")
     * @param ParamFetcher $paramFetcher
     * @param BookReviewRepository $bookReviewRepository
     * @return JsonResponse
     */
    #[Get("/api/v1/reviews")]
    #[QueryParam(name: "page", requirements: '\d+', strict: true, nullable: true, allowBlank: false)]
    public function getReviewsByPage(
        ParamFetcher $paramFetcher,
        BookReviewRepository $bookReviewRepository,
    ):JsonResponse{

        $page = $paramFetcher->get("page");
        if($page == null){
            $page = 1;
        }
        $data = $bookReviewRepository->findPubliclyAvailable($page);

        return  $this->jsonResponse($data);
    }


    /**
     * Get a book with the given id
     * @Response(
     *   description="Successfully reuturned  book review by the specified id",
     *   response= 200,
     *   @Model(type=BookReview::class)
     * )
     *  @Response(
     *     response=404,
     *     description="The review with the specified id not found"
     * )
     * @Tag(name="Book Reviews")
     * @Security(name="Bearer")
     * @param BookReview $bookReview
     * @return JsonResponse
     */
    #[Get("/api/v1/reviews/{id}")]
    public function getReviewById(
        BookReview $bookReview
    ):JsonResponse{
        return $this->jsonResponse(
            $bookReview
        );
    }



    /**
     *  Create a book review
     *  @RequestBody(
     *     description="Successfully returned the  created  review",
     *     @JsonContent(ref=@Model(type=CreateBookReviewModel::class))
     * )
     *  @Response(
     *     description="Review successfully created",
     *     response=201,
     *     @Model(type=BookReview::class)
     * )
     * @Response(
     *     response=400,
     *     description="Bad data given to the request, see error messages",
     *     @JsonContent(type="object",
     *     @Property(property="error",type="string", example= "You have not provided a title")
     * )
     * )
     * @Security(name="Bearer")
     * @Tag(name="Book Reviews")
     */
    #[Post("/api/v1/reviews")]
    public function createReview(
        Request                    $request,
        BookRepository $bookRepository,
        UserRepository             $userRepository,
        GoogleBooksDTOUtils $googleBooksDTOUtils,
        AwsImageUtils $awsImageUtils
    ):JsonResponse{

        /**
         * @var CreateBookReviewModel $createModel
         */
        $createModel  = $this->serializer->deserialize(
            data: $request->getContent(),
            type: CreateBookReviewModel::class,
            format: 'json');

        $manager = $this->getDoctrine()->getManager();
        $validationErrors =  $this->validator->validate($createModel);

        if(count($validationErrors) === 0){
            $bookReview = new BookReview();
            foreach ($createModel->getSections() as $section){
                $bookReview->addSection($section);
                $manager->persist($section);
            }
            $bookReview->setSections(
                new ArrayCollection($createModel->getSections())
            );
            $bookReview->setTitle($createModel->getTitle());

            $user = $userRepository->findByEmail(
                $this->getJWTPayload()->getEmail()
            );
            $bookReview->setCreator($user);

            if($createModel->getGoogleBookID() === null){
                $book = $bookRepository->find($createModel->getBookID());
            }else {
                $book = $googleBooksDTOUtils->getGoogleBookFromRequest($createModel);
            }
            if($book === null){
                return $this->errorResponse(self::ERROR_BOOK_NOT_FOUND);
            }

            $manager->persist($book);
            $bookReview->setBook($book);
            $bookReview->setPending(
                $this->getJWTPayload()->isUserModerator()
            );

            $reviewImage = $awsImageUtils->uploadBase64ImageToBucketeer(
               $createModel->getBase64Image()
            );
            if($reviewImage == null){
                return $this->errorResponse(self::INVALID_BASE64_IMAGE);
            }
            $bookReview->setFrontImage($reviewImage);

            $manager-> persist($bookReview);
            $manager-> flush($bookReview);
            return $this->jsonResponse(
                $bookReview
            );

        }
        return $this->constraintViolationResponse($validationErrors);
    }


    /**
     * Get the comments for a review
     * @Response(
     *     description="Successfully returned the comments for the review with the specified id",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref=@Model(type=Comment::class))
     * )
     * )
     *  @Response(
     *     response=404,
     *     description="The review with the specified id not found"
     * )
     * @Tag(name="Book Reviews")
     * @Security(name="Bearer")
     * @param BookReview $bookReview
     * @return JsonResponse
     */
    #[Get("/api/v1/reviews/{id}/comments")]
    public function getComments(
        BookReview $bookReview
    ):JsonResponse{
        return $this->jsonResponse(
            $bookReview->getComments()
        );
    }
    /**
     * Get the comment with the given ID of a review with the given id
     * @Response(
     *     description="Sucessfully returned comment with that ID of the given review",
     *     response=200,
     *     @Model(type= Comment::class)
     * )
     * @Response(
     *     description="The comment was not found or the review was not found",
     *     response=404
     * )
     * @Tag(name="Book Reviews")
     * @Security(name="Bearer")
     * @return JsonResponse
     */
    #[Get("/api/v1/reviews/{reviewID}/comments/{commentID}")]
    #[Entity('bookReview', options: ['id' => 'reviewID'])]
    #[Entity('comment', options: ['id' => 'commentID'])]

    public function getComment(
        Comment $comment,

    ):JsonResponse{
        return $this->jsonResponse(
             $comment
        );
    }



    /**
     * Delete a book review with the specified id
     * @Response(
     *     description="Delete a book review with the specified id",
     *     response=204,
     *     @JsonContent(type="object")
     * )
     * @Response(
     *     description="Unauthorized to delete this",
     *     response=403,
     *     @JsonContent(@Property (property="error",type="string",example="Not authorized to delete this resource"))
     * )
     *  @Response(
     *     response=404,
     *     description="The review with the specified id not found"
     * )
     * @Parameter  (
     *     name="id",
     *     in = "path",
     *     @Schema(type="integer")
     * )
     *
     * @Security(name="Bearer")
     * @Tag(name="Book Reviews")
     * @param BookReview $bookReview
     * @return JsonResponse
     */
    #[Delete("/api/v1/reviews/{id}")]
    public function deleteBookReview(
       BookReview $bookReview
    ):JsonResponse{
      //allow deletion only by user who created the review or by moderator
      $jwtPayload = $this->getJWTPayload();
      if($jwtPayload->getEmail() === $bookReview->getCreator()->getEmail()
         ||in_array("ROLE_MODERATOR",$jwtPayload->getRoles()) ){
         return $this->jsonResponse(
             new StdClass(),
             statusCode: SymfonyResponse::HTTP_NO_CONTENT
         );
      }
      return $this->errorResponse(
           "Not authorized to delete this  resource",
            SymfonyResponse::HTTP_FORBIDDEN
      );
    }

}