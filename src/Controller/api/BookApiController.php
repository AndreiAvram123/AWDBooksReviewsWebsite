<?php

namespace App\Controller\api;

use App\BookApi\GoogleBookDTO;
use App\BookApi\GoogleBooksDTOUtils;
use App\Entity\Book;
use App\Entity\BookReview;
use App\Form\BookType;
use App\Repository\BookAuthorRepository;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\GoogleBookApiRepository;
use App\RequestModels\CreateBookRequestModel;
use App\RequestModels\CreateBookReviewModel;
use App\ResponseModels\SearchModel;
use App\utils\entities\BookUtils;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\RequestBody;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Tag;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use OpenApi\Annotations\Property;

class BookApiController extends BaseRestController
{
    /**
     * Get all books
     * @Response(
     *  description="Successfully returned all book",
     *   response= 200,
     *   @Model(type=Book::class)
     * )
     * @Tag(name="Books")
     * @Security(name="Bearer")
     * @return JsonResponse
     */

    #[Get("/api/v1/books")]
    #[QueryParam(name: "page", requirements: '\d+', strict: true, nullable: true, allowBlank: false)]
    public function getAllBooks(
        ParamFetcher $paramFetcher,
        BookRepository $bookRepository
    ):JsonResponse{
        $page = $paramFetcher->get("page");
        if($page == null){
            $page = 1;
        }
        $books = $bookRepository->findPubliclyAvailable($page);
        return $this->jsonResponse($books);

    }

    /**
     * Create a book
     * @Response(
     *     description="Book successfully created",
     *     response=201,
     *     @Model(type=BookReview::class)
     * )
     *  @RequestBody(
     *     description="Data for creating a review",
     *     @JsonContent(ref=@Model(type=CreateBookRequestModel::class))
     * )
     *  @Response(
     *     response=400,
     *     description="Bad data given to the request, see error messages",
     *     @JsonContent(type="object",
     *     @Property(property="error",type="string", example= "You have not provided a title")
     * ))
     *
     * @Security(name="Bearer")
     * @Tag(name="Books")
     */
    #[Post("/api/v1/books")]
    public function createBook(
        Request $request,
        BookCategoryRepository $bookCategoryRepository,
        BookAuthorRepository $bookAuthorRepository,
        BookUtils $bookUtils
    ):JsonResponse{
        /** @var CreateBookRequestModel $createBookRequest */
        $createBookRequest = $this->serializer->deserialize(
            $request->getContent(),
            type: CreateBookRequestModel::class,
            format: 'json'
        );
        $validationErrors = $this->validator->validate($createBookRequest);
        if(count($validationErrors) === 0){
              $book = $bookUtils->createBookFromRequest(
                  $createBookRequest,
                  $bookCategoryRepository,
                  $bookAuthorRepository
              );
              $this->persistAndFlush($book);
              return $this->jsonResponse(
                  data: $book,
                  statusCode: SymfonyResponse::HTTP_CREATED
              );
        }
        return $this->constraintViolationResponse(
            $validationErrors
        );
    }


    /**
     * Search a book by query
     * @Response(
     *     response=200,
     *     description="Search results successfully returned",
     *     @JsonContent(
     *     type="array",
     *     @Items(ref=@Model(type=SearchModel::class))
     * )
     * )
     * @OA\Parameter(
     *     name="query",
     *     in = "query",
     *     required=true,
     *     @OA\Schema(type="string")
     * )
     * @Tag(name="Search")
     * @Security(name="Bearer")
     */
    #[Get("/api/v1/books/search")]
    #[QueryParam(
        name: "query",
        requirements: "[\sa-zA-Z0-9]+",
        strict: true,
    )]
    public function searchBookByTitle(
        ParamFetcherInterface   $paramFetcher,
        GoogleBookApiRepository $googleBooksRepository,
        BookRepository          $bookRepository,
        GoogleBooksDTOUtils     $googleBooksDTOUtils
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

    /**
     * Get a book by its id
     * @Response(
     *     response=200,
     *     description="Successfully returned Book with the given ID",
     *     @JsonContent(ref= @Model(type=Book::class) )
     * )
     * @Response(
     *     response=404,
     *     description="The book with the specified id not found"
     * )
     * @Tag(name="Books")
     * @Security(name="Bearer")
     */
   #[Get("/api/v1/books/{id}")]
    public function getBookById(
        Book $book
   ):JsonResponse{
        return $this->jsonResponse(
            $book
        );
   }

    /**
     * Get the reviews of a book with the specified id
     * @Response(
     *   response=200,
     *   description="Successfully returned the reviews for the book with the specified id",
     *   @JsonContent(
     *     type="array",
     *     @Items(ref= @Model(type=BookReview::class))
     * )
     * )
     * @Response(
     *     response=404,
     *     description="The book with the specified id not found"
     * )
     * @OA\Parameter(
     *     name="id",
     *     in = "path",
     *     @OA\Schema(type="integer")
     * )
     * @Tag(name="Books")
     * @Security(name="Bearer")
     * @param Book $book
     * @return JsonResponse
     */
   #[Get("/api/v1/books/{id}/reviews")]
   public function getBookReviews(
       Book $book
   ):JsonResponse{
        return $this->jsonResponse(
            $book->getBookReviews()
        );
   }

}