<?php

namespace App\Controller\api;

use App\Repository\BookAuthorRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\BookAuthor;

class BookAuthorApiController extends BaseRestController
{

    /**
     * @Response(
     *     response=200,
     *     description="Return all the available authors",
     *     @JsonContent(
     *       type="array",
     *       @Items(ref= @Model(type= BookAuthor::class))
     *     )
     *  )
     *
     * @Security(name="Bearer")
     * @Tag(name="Authors")
     * @param BookAuthorRepository $bookAuthorRepository
     * @return JsonResponse
     */
    #[Get("/api/v1/authors")]
   public function getAllAuthors(
        BookAuthorRepository $bookAuthorRepository
    ):JsonResponse{
       return $this->jsonResponse(
           $bookAuthorRepository->findAll()
       );
   }
}