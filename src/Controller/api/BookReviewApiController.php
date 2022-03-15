<?php

namespace App\Controller\api;



use App\Entity\Book;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\ReviewSectionRepository;
use App\Repository\UserRepository;
use App\RequestModels\CreateBookReviewModel;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;


class BookReviewApiController extends BaseRestController
{

    private const ERROR_BOOK_NOT_FOUND = "The book with this id was not found";

    #[Get("/api/reviews")]
    public function getReviewsByPage(
        Request $request,
        BookReviewRepository $bookReviewRepository,

    ):JsonResponse{
        $page = $request->query->get("page");
        if($page === null){
            $page = 1;
        }
        $data = $bookReviewRepository->findPubliclyAvailable($page);
        $serializedData = $this->serializer->serialize(
            data: $data,
            format: 'json');

       return  $this->jsonResponse($serializedData);
    }

    #[Get("/api/reviews/{id}")]
    public function getReviewById(
        BookReview $bookReview
    ):JsonResponse{
        return $this->jsonResponse(
            $bookReview
        );
    }



    #[Post("/api/reviews")]
   public function createReview(
       Request $request,
       BookRepository $bookRepository,
       UserRepository $userRepository
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
                $this->getEmailFromToken()
            );
           $bookReview->setCreator($user);

           if($createModel->getGoogleBookID() === null){
               $book = $bookRepository->find($createModel->getBookID());
               if($book === null){
                   return $this->notAcceptableResponse([self::ERROR_BOOK_NOT_FOUND]);
               }
               $bookReview->setBook($book);
           }else{
               //todo
               //should perform a check here as well
               $bookReview->setGoogleBookID($createModel->getGoogleBookID());
           }

           $bookReview->setPending(true);

           $manager-> persist($bookReview);
           $manager-> flush($bookReview);
           return $this->jsonResponse(
               $bookReview
           );

       }
       return $this->constraintViolationResponse($validationErrors);
   }



}