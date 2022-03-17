<?php

namespace App\Controller\api;

use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\UserRepository;
use App\ResponseModels\SearchResponseModel;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchApiController extends BaseRestController
{

    #[Get("/api/v1/search")]
    #[QueryParam(
        name: "query",
        requirements: "[\sa-zA-Z0-9]+",
        strict: true
    )]
    public function search(
        ParamFetcherInterface    $paramFetcher,
        Request $request,
        BookReviewRepository $bookReviewRepository,
        BookRepository $bookRepository,
        UserRepository $userRepository
    ):JsonResponse{
        $query =$paramFetcher->get('query');
        $bookReviews = $bookReviewRepository->findByTitle($query);
        $books = $bookRepository->searchByTitle($query);
        $users = $userRepository->findByUsernameQuery($query);
        return $this->jsonResponse(
            data: new SearchResponseModel(
                bookReviews: $bookReviews,books: $books, users: $users
            )
        );
    }
}