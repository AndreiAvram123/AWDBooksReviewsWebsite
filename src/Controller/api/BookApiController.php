<?php

namespace App\Controller\api;

use App\Repository\GoogleBooksRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookApiController extends BaseRestController
{

    #[Get("/api/books")]
    #[QueryParam(
        name: "query",
        requirements: "[a-zA-Z0-9]+",
        strict: true
    )]
    public function searchBookByTitle(
       ParamFetcherInterface $paramFetcher,
        GoogleBooksRepository $googleBooksRepository
    ): JsonResponse
    {
       $query = $paramFetcher->get('query');
       return JsonResponse::fromJsonString(
           $googleBooksRepository->searchByTitle($query)
       );
    }
}