<?php

namespace App\Controller\api;

use App\Repository\BookRepository;
use App\Repository\ExclusiveBookRepository;
use App\Repository\GoogleBooksApiRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookApiController extends BaseRestController
{

    #[Get("/api/books")]
    #[QueryParam(
        name: "query",
        requirements: "[\sa-zA-Z0-9]+",
        strict: true
    )]
    public function searchBookByTitle(
        ParamFetcherInterface    $paramFetcher,
        GoogleBooksApiRepository $googleBooksRepository,
        BookRepository           $bookRepository
    ): JsonResponse
    {
        $query = $paramFetcher->get('query');
        return $this->jsonResponse([
                "googleBooks"=> $googleBooksRepository->searchByTitle($query),
                "exclusiveBooks"    => $bookRepository->searchByTitle($query)
            ]
        );
    }
    #[Get("/api/v1/books/exclusive")]
    public function getExclusiveBooks(
        ExclusiveBookRepository $exclusiveBookRepository
    ):JsonResponse{
        return $this->jsonResponse(
            $exclusiveBookRepository->findAll()
        );
    }
}