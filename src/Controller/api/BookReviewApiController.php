<?php

namespace App\Controller\api;


use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\GoogleBookApiRepository;
use App\Repository\UserRepository;
use App\RequestModels\CreateBookReviewModel;
use App\utils\aws\AwsImageUtils;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class BookReviewApiController extends BaseRestController
{

    private const ERROR_BOOK_NOT_FOUND = "The book with this id was not found";
    private const INVALID_BASE64_IMAGE = "Invalid base 64 image";

    #[Get("/api/v1/reviews")]

    public function getReviewsByPage(
        ParamFetcher $paramFetcher,
        Request $request,
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
                return $this->notAcceptableResponse(self::ERROR_BOOK_NOT_FOUND);
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
                return $this->notAcceptableResponse(self::INVALID_BASE64_IMAGE);
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

    #[Get("/api/v1/reviews/{id}/comments")]
    public function getComments(
        BookReview $bookReview
    ):JsonResponse{
        return $this->jsonResponse(
            $bookReview->getComments()
        );
    }

}