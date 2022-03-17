<?php

namespace App\Controller\api;

use App\BookApi\GoogleBookDTO;
use App\BookApi\GoogleBooksDTOUtils;
use App\Repository\BookRepository;
use App\Repository\ExclusiveBookRepository;
use App\Repository\GoogleBooksApiRepository;
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
        strict: true
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
        array_map(function (GoogleBookDTO $googleBookDTO) use ($googleBooksDTOUtils){
                 return $googleBooksDTOUtils->convertDTOToEntity($googleBookDTO);
    },$googleBooks);
        $localBooks = $bookRepository->searchByTitle($query);
        return $this->jsonResponse(
            array_merge($localBooks,$googleBooks)
        );
    }
}