<?php

namespace App\Controller\api;

use App\Entity\Book;
use App\Repository\BookCategoryRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\JsonContent;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\BookCategory;

class CategoryApiController extends BaseRestController
{
    /**
     * Get all categories
     * @Response (
     *     description="Successfully returned all the categories",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(
     *       ref= @Model(type=BookCategory::class)
     *    )
     * )
     * )
     *  @Response(
     *     response=404,
     *     description="The category with the specified id not found"
     * )
     *
     *
     * @Tag(name="Categories")
     * @Security(name="Bearer")
     * @param BookCategoryRepository $bookCategoryRepository
     * @return JsonResponse
     */
    #[Get("/api/v1/categories")]
    public function getAllCategories(
        BookCategoryRepository $bookCategoryRepository
    ):JsonResponse{

        $categories = $bookCategoryRepository->findAll();
        return $this->jsonResponse(
            $categories
        );
    }

    /**
     * Get books from a category
     * @Response(
     *     description="Successfully returned the books from the category with the specified id",
     *     response=200,
     *     @JsonContent(
     *     type="array",
     *     @Items(ref = @Model(type=Book::class))
     * ))
     * @Tag(name="Categories")
     * @Security(name="Bearer")
     *
     * @param BookCategory $bookCategory
     * @return JsonResponse
     */
    #[Get("/api/v1/categories/{id}/books")]
    public function getBooksFromCategory(
        BookCategory $bookCategory
    ):JsonResponse{
        return $this->jsonResponse(
            $bookCategory->getBooks()
        );
    }
}