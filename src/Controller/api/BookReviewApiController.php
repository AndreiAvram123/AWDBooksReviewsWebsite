<?php

namespace App\Controller\api;


use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\GoogleBookApiRepository;
use App\Repository\UserRepository;
use App\Entity\Comment;
use App\RequestModels\CreateBookReviewModel;
use App\utils\aws\AwsImageUtils;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class BookReviewApiController extends BaseRestController
{

    private const ERROR_BOOK_NOT_FOUND = "The book with this id was not found";
    private const INVALID_BASE64_IMAGE = "Invalid base 64 image";

    /**
     * @Response(
     *     description="Return book reviews by page",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref= @Model(type=BookReview::class))
     * )
     * )
     * @Parameter(
     *     name="page",
     *     in = "query",
     *     allowEmptyValue=true,
     *     @Schema(type="integer")
     *  )
     * @Tag(name="Book Reviews")
     * @Security(name="Bearer")
     * @param ParamFetcher $paramFetcher
     * @param BookReviewRepository $bookReviewRepository
     * @return JsonResponse
     */
    #[Get("/api/v1/reviews")]
    public function getReviewsByPage(
        ParamFetcher $paramFetcher,
        BookReviewRepository $bookReviewRepository,
    ):JsonResponse{
        $page = $paramFetcher->get("page");
        if($page === null){
            $page = 1;
        }
        $data = $bookReviewRepository->findPubliclyAvailable($page);
        $serializedData = $this->serializer->serialize(
            data: $data,
            format: 'json');

        return  $this->jsonResponse($serializedData);
    }


    /**
     * @Response(
     *   description="Return the Book review by the specified id",
     *   response= 200,
     *   @Model(type=BookReview::class)
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


//todo
//maybe an exception catcher

    /**
     * @Response(
     *     description="Create a review",
     *     response=201,
     *     @Model(type=BookReview::class)
     * )
     *  @RequestBody(
     *     description="Data for creating a review",
     *     @JsonContent(ref=@Model(type=CreateBookReviewModel::class))
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
     * @Response(
     *     description="Return the comments for the review with the specified id",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref=@Model(type=Comment::class))
     * )
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

}