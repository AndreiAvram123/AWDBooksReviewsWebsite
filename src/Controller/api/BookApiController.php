<?php

namespace App\Controller\api;

use App\BookApi\GoogleBookDTO;
use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\Book;
use App\Entity\BookReview;
use App\Repository\BookRepository;
use App\Repository\GoogleBooksApiRepository;
use App\ResponseModels\SearchModel;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookApiController extends BaseRestController
{

    #[Get("/api/v1/books/search")]
    #[QueryParam(
        name: "query",
        requirements: "[\sa-zA-Z0-9]+",
        strict: true,
        allowBlank: false
    )]
    public function searchBookByTitle(
        ParamFetcherInterface    $paramFetcher,
        GoogleBooksApiRepository $googleBooksRepository,
        BookRepository           $bookRepository,
        GoogleBooksDTOUtils $googleBooksDTOUtils
    ): JsonResponse
    {
        $query = $paramFetcher->get('query');
        $googleBooks = $googleBooksRepository->searchByTitle($query);
       $mappedGoogleBooks =  array_map(function (GoogleBookDTO $googleBookDTO) use ($googleBooksDTOUtils){
                 return $googleBooksDTOUtils->convertDTOToEntity($googleBookDTO);
     },$googleBooks);

        $localBooks = $bookRepository->searchByTitle($query);
        $allBooks = array_merge($localBooks,$mappedGoogleBooks);
        $responseData  = array_map(function (Book $book){
            return SearchModel::convertBookToSearchModel($book);
        },$allBooks);
        return $this->jsonResponse(
           $responseData
        );
    }

   #[Get("/api/v1/books/{id}")]
    public function getBookById(
        Book $book
   ):JsonResponse{
        return $this->jsonResponse(
            $book
        );
   }

   #[Get("/api/v1/books/{id}/reviews")]
   public function getBookReviews(
       Book $book
   ):JsonResponse{
        return $this->jsonResponse(
            $book->getBookReviews()
        );
   }

}