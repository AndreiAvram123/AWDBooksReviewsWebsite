<?php

namespace App\Controller\api;

use App\Repository\BookRepository;
use App\Repository\BookReviewRepository;
use App\Repository\UserRepository;
use App\ResponseModels\SearchModel;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use OpenApi\Annotations\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations\Property;
use App\Entity\BookReview;
use App\Entity\Book;
use App\Entity\User;

class SearchApiController extends BaseRestController
{

    /**
     * Search books,reviews an authors
     * @Response(
     *     description="Successfully returned   books, reviews and authors matching query",
     *     response=200,
     *     @JsonContent(
     *      type="object",
     *      @Property(property="bookReviews", type="array", @Items(ref= @Model(type=BookReview::class))),
     *      @Property(property="books", type="array", @Items(ref = @Model(type=Book::class))),
     *      @Property(property="users", type="array", @Items(ref = @Model(type=User::class)))
     * )
     * )
     * @Parameter(
     *     name="query",
     *     in = "query",
     *     required=true,
     *     allowEmptyValue=false,
     *     @Schema(type="string")
     * )
     * @Tag(name="Search")
     * @Security(name="Bearer")
     */
    #[Get("/api/v1/search")]
    #[QueryParam(
        name: "query",
        requirements: "[\sa-zA-Z0-9]+",
        strict: true,
        nullable: false,

    )]
    public function search(
        ParamFetcherInterface    $paramFetcher,
        BookReviewRepository $bookReviewRepository,
        BookRepository $bookRepository,
        UserRepository $userRepository
    ):JsonResponse{
        $query =$paramFetcher->get('query');
        $bookReviews = $bookReviewRepository->findByTitle($query);
        $books = $bookRepository->searchByTitle($query);
        $users = $userRepository->findByUsernameQuery($query);
        return $this->jsonResponse(
             [
                  "bookReviews"=>$bookReviews,
                  "books" => $books,
                  "users"=> $users
             ]);
    }
}