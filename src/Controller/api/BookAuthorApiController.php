<?php

namespace App\Controller\api;

use App\Repository\BookAuthorRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookAuthorApiController extends BaseRestController
{
    #[Get("/api/v1/authors")]
   public function getAllAuthors(
        BookAuthorRepository $bookAuthorRepository
    ):JsonResponse{
       return $this->jsonResponse(
           $bookAuthorRepository->findAll()
       );
   }
}