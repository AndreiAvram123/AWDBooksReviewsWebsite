<?php

namespace App\Controller\api;



use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class BookReviewApiController extends BaseRestController
{


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


    #[Post("/api/reviews")]
   public function createReview(
       Request $request,
       BookRepository $bookRepository
    ):JsonResponse{

         $bookReview  = $this->serializer->deserialize(
           data: $request->getContent(),
           type: BookReview::class,
           format: 'json'
           );

       if($this->isDataValid($bookReview)){

       }

       return $this -> jsonResponse($bookReview);
   }



}