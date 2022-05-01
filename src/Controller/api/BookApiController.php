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
use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Tag;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BookApiController extends BaseRestController
{

    #[Post("/api/v1/books")]
    public function createBook(
        Request $request,
        BookCategoryRepository $bookCategoryRepository,
        BookAuthorRepository $bookAuthorRepository
    ):JsonResponse{
        /** @var CreateBookRequestModel $createBookRequest */
        $createBookRequest = $this->serializer->deserialize(
            $request->getContent(),
            type: CreateBookRequestModel::class,
            format: 'json'
        );
        $validationErrors = $this->validator->validate($createBookRequest);
        if(count($validationErrors) === 0){
              $book = $this->createBookFromRequest(
                  $createBookRequest,
                  $bookCategoryRepository,
                  $bookAuthorRepository
              );
              $this->persistAndFlush($book);
              return $this->jsonResponse(
                  data: $book,
                  statusCode: 201
              );
        }
        return $this->constraintViolationResponse(
            $validationErrors
        );
    }

    private function getCategoriesFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookCategoryRepository $bookCategoryRepository
    ):ArrayCollection{
        $categories = [];
         foreach ($createBookRequestModel->getCategoriesIDs() as $categoriesID){
                $bookCategory = $bookCategoryRepository->find($categoriesID);
                $categories[] = $bookCategory;
         }
         return new ArrayCollection($categories);
    }
    private function getAuthorsFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookAuthorRepository $bookAuthorRepository
    ):ArrayCollection{
        $authors = [];
        foreach ($createBookRequestModel->getAuthors() as $authorID){
            $author = $bookAuthorRepository->find($authorID);
            $authors[] = $author;
        }
        return new ArrayCollection($authors);
        }

    private function createBookFromRequest(
        CreateBookRequestModel $createBookRequestModel,
        BookCategoryRepository $bookCategoryRepository,
        BookAuthorRepository $bookAuthorRepository
    ):Book{
        $book = new Book();
        $book->setTitle($createBookRequestModel->getTitle());
        $book->setCategories($this->getCategoriesFromRequest(
            $createBookRequestModel,
            $bookCategoryRepository
        ));
         $book->setAuthors(
             $this->getAuthorsFromRequest(
                 $createBookRequestModel,
                 $bookAuthorRepository
             )
         );
         return $book;
    }


    /**
     * Search a book by query
     * @Response(
     *     response=200,
     *     description="Return search results by query",
     *     @JsonContent(
     *     type="array",
     *     @Items(ref=@Model(type=SearchModel::class))
     * )
     * )
     * @OA\Parameter(
     *     name="query",
     *     in = "query",
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
        allowBlank: false
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
     *     description="Return the book by its id",
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
     *   description="Return the reviews for a book with the specified id",
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